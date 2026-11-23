<?php

namespace App\Services;

use App\Support\YoutubeSettings;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Talks to YouTube for the LMS: connect the campus channel (Google sign-in once), then upload videos to it.
 * Videos go up as "Unlisted" in small pieces (resumable upload), so a big class recording never has to fit in
 * memory and an interrupted upload can carry on where it stopped.
 */
class YoutubeService
{
    private const AUTH = 'https://accounts.google.com/o/oauth2/v2/auth';
    private const TOKEN = 'https://oauth2.googleapis.com/token';
    private const API = 'https://www.googleapis.com/youtube/v3';
    private const UPLOAD = 'https://www.googleapis.com/upload/youtube/v3/videos';
    // One permission that covers uploading, reading the channel and managing its playlists.
    private const SCOPES = 'https://www.googleapis.com/auth/youtube';

    /** Pieces of 8 MB (YouTube wants a multiple of 256 KB). */
    public const CHUNK = 8 * 1024 * 1024;

    /** The Google sign-in address the admin is sent to. */
    public function authUrl(string $redirectUri, string $state): string
    {
        if (! YoutubeSettings::hasKeys()) {
            throw new RuntimeException('Save the Client ID and Client Secret first.');
        }

        return self::AUTH.'?'.http_build_query([
            'client_id' => YoutubeSettings::get('client_id'),
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => self::SCOPES,
            'access_type' => 'offline',   // gives the long-lasting key
            'prompt' => 'consent',        // always give it, even if the admin connected before
            'include_granted_scopes' => 'true',
            'state' => $state,
        ]);
    }

    /** After Google sends the admin back with a code: swap it for the long-lasting key and note the channel. */
    public function connect(string $code, string $redirectUri): string
    {
        $response = Http::asForm()->timeout(30)->post(self::TOKEN, [
            'code' => $code,
            'client_id' => YoutubeSettings::get('client_id'),
            'client_secret' => YoutubeSettings::get('client_secret'),
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
        ]);

        if (! $response->successful()) {
            throw new RuntimeException('Google did not accept the sign-in ('.($response->json('error_description') ?: $response->json('error') ?: $response->status()).'). Check the Client ID, Client Secret and the redirect address, then try again.');
        }

        $refresh = $response->json('refresh_token');
        if (! $refresh) {
            throw new RuntimeException('Google did not give a long-lasting key. Remove the app at myaccount.google.com/permissions and connect again.');
        }

        YoutubeSettings::put('refresh_token', $refresh);
        YoutubeSettings::put('scopes', (string) $response->json('scope'));
        Cache::put('youtube.access_token', $response->json('access_token'), now()->addSeconds(max(60, (int) $response->json('expires_in', 3600) - 300)));

        $channel = $this->api()->get(self::API.'/channels', ['part' => 'snippet', 'mine' => 'true']);
        $item = $channel->successful() ? ($channel->json('items.0') ?? null) : null;

        if (! $item) {
            YoutubeSettings::disconnect();
            throw new RuntimeException('Signed in, but this Google account has no YouTube channel. Create the channel on youtube.com first, then connect again.');
        }

        YoutubeSettings::put('channel_id', $item['id'] ?? null);
        YoutubeSettings::put('channel_title', $item['snippet']['title'] ?? 'YouTube channel');

        return (string) YoutubeSettings::get('channel_title');
    }

    /** A key that works for about an hour, made from the long-lasting key. */
    public function accessToken(): string
    {
        if (! YoutubeSettings::isConnected()) {
            throw new RuntimeException('YouTube is not connected yet. Connect it on the YouTube Connection page.');
        }

        return Cache::remember('youtube.access_token', now()->addMinutes(50), function () {
            $response = Http::asForm()->timeout(30)->post(self::TOKEN, [
                'client_id' => YoutubeSettings::get('client_id'),
                'client_secret' => YoutubeSettings::get('client_secret'),
                'refresh_token' => YoutubeSettings::get('refresh_token'),
                'grant_type' => 'refresh_token',
            ]);

            if (! $response->successful()) {
                $why = $response->json('error') === 'invalid_grant'
                    ? 'The YouTube sign-in has expired or was removed. Connect YouTube again on the YouTube Connection page.'
                    : 'Google did not give a YouTube key ('.($response->json('error_description') ?: $response->status()).').';
                throw new RuntimeException($why);
            }

            return $response->json('access_token');
        });
    }

    private function api()
    {
        return Http::withToken($this->accessToken())->acceptJson()->timeout(30);
    }

    /**
     * Ask YouTube for an upload "session". Returns its address; keep it to carry on after an interruption.
     */
    public function startUpload(string $path, string $title, string $description = '', string $privacy = 'unlisted', string $mime = 'video/mp4'): string
    {
        if (! is_file($path)) {
            throw new RuntimeException('The video file is missing on the server.');
        }

        $response = Http::withToken($this->accessToken())
            ->withHeaders(['X-Upload-Content-Length' => (string) filesize($path), 'X-Upload-Content-Type' => $mime])
            ->timeout(60)
            ->post(self::UPLOAD.'?uploadType=resumable&part=snippet,status', [
                'snippet' => [
                    'title' => self::cleanTitle($title),
                    'description' => mb_substr(str_replace(['<', '>'], '', $description), 0, 4900),
                    'categoryId' => '27',   // Education
                ],
                'status' => [
                    'privacyStatus' => in_array($privacy, ['unlisted', 'private', 'public'], true) ? $privacy : 'unlisted',
                    'selfDeclaredMadeForKids' => false,
                ],
            ]);

        if (! $response->successful() || ! $response->header('Location')) {
            throw new RuntimeException($this->explain($response, 'start the upload'));
        }

        return $response->header('Location');
    }

    /**
     * Send the file in pieces (from where YouTube already has it) and return the new video's ID.
     *
     * @param  callable(int, int): void|null  $progress  called with (bytes sent, total bytes) after each piece
     */
    public function sendFile(string $sessionUri, string $path, string $mime = 'video/mp4', ?callable $progress = null): string
    {
        $total = filesize($path);
        $handle = fopen($path, 'rb');

        if ($handle === false) {
            throw new RuntimeException('The video file could not be opened.');
        }

        try {
            $start = $total === 0 ? 0 : $this->bytesAlreadyThere($sessionUri, $total);

            while (true) {
                fseek($handle, $start);
                $chunk = $total === 0 ? '' : (string) fread($handle, self::CHUNK);
                $end = $start + strlen($chunk) - 1;

                $response = $this->putChunk($sessionUri, $chunk, $start, $end, $total, $mime);

                if (in_array($response->status(), [200, 201], true)) {
                    $id = $response->json('id');
                    if (! $id) {
                        throw new RuntimeException('YouTube accepted the video but gave no ID.');
                    }
                    $progress && $progress($total, $total);

                    return (string) $id;
                }

                if ($response->status() === 308) {
                    $start = $this->nextByte($response->header('Range'), $start + strlen($chunk));
                    $progress && $progress($start, $total);

                    continue;
                }

                throw new RuntimeException($this->explain($response, 'upload the video'));
            }
        } finally {
            fclose($handle);
        }
    }

    /** One piece, tried a few times if the connection hiccups. */
    private function putChunk(string $sessionUri, string $chunk, int $start, int $end, int $total, string $mime)
    {
        $attempt = 0;

        while (true) {
            $attempt++;

            try {
                $headers = ['Content-Type' => $mime, 'Content-Range' => $total === 0 ? 'bytes */0' : "bytes {$start}-{$end}/{$total}"];
                $response = Http::withHeaders($headers)->withOptions(['allow_redirects' => false])->timeout(300)
                    ->withBody($chunk, $mime)->put($sessionUri);
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                if ($attempt >= 5) {
                    throw new RuntimeException('The connection to YouTube kept failing. Try again later.');
                }
                sleep(2 * $attempt);

                continue;
            }

            if ($response->serverError() && $attempt < 5) {
                sleep(2 * $attempt);

                continue;
            }

            return $response;
        }
    }

    /** How many bytes of this file YouTube already holds (0 for a new upload). */
    private function bytesAlreadyThere(string $sessionUri, int $total): int
    {
        $response = Http::withHeaders(['Content-Range' => "bytes */{$total}"])->withOptions(['allow_redirects' => false])
            ->timeout(60)->withBody('', 'video/mp4')->put($sessionUri);

        return $response->status() === 308 ? $this->nextByte($response->header('Range'), 0) : 0;
    }

    /** "bytes=0-8388607" -> 8388608 (the next byte to send). */
    private function nextByte(?string $range, int $fallback): int
    {
        return $range && preg_match('/bytes=\d+-(\d+)/', $range, $m) ? ((int) $m[1]) + 1 : $fallback;
    }

    /**
     * Every playlist on the connected channel, as [['id' => ..., 'title' => ...], ...].
     */
    public function playlists(): array
    {
        $out = [];
        $page = null;

        do {
            $query = ['part' => 'snippet', 'mine' => 'true', 'maxResults' => 50];
            if ($page) {
                $query['pageToken'] = $page;
            }

            $response = $this->api()->get(self::API.'/playlists', $query);

            if (! $response->successful()) {
                throw new RuntimeException($this->explain($response, 'read the playlists'));
            }

            foreach ($response->json('items', []) as $item) {
                $out[] = ['id' => $item['id'], 'title' => (string) ($item['snippet']['title'] ?? '')];
            }

            $page = $response->json('nextPageToken');
        } while ($page && count($out) < 500);

        return $out;
    }

    /** Make a new playlist on the channel and return its ID. */
    public function createPlaylist(string $title, string $description = '', string $privacy = 'unlisted'): string
    {
        $response = $this->api()->post(self::API.'/playlists?part=snippet,status', [
            'snippet' => ['title' => self::cleanTitle($title), 'description' => mb_substr(str_replace(['<', '>'], '', $description), 0, 4900)],
            'status' => ['privacyStatus' => in_array($privacy, ['unlisted', 'private', 'public'], true) ? $privacy : 'unlisted'],
        ]);

        if (! $response->successful() || ! $response->json('id')) {
            throw new RuntimeException($this->explain($response, 'create the playlist'));
        }

        return (string) $response->json('id');
    }

    /** Put a video into a playlist. */
    public function addToPlaylist(string $playlistId, string $videoId): void
    {
        $response = $this->api()->post(self::API.'/playlistItems?part=snippet', [
            'snippet' => ['playlistId' => $playlistId, 'resourceId' => ['kind' => 'youtube#video', 'videoId' => $videoId]],
        ]);

        if (! $response->successful()) {
            throw new RuntimeException($this->explain($response, 'add the video to the playlist'), $response->status());
        }
    }

    /** Delete one of our own videos (used to clean up the test video). */
    public function deleteVideo(string $videoId): void
    {
        $response = $this->api()->delete(self::API.'/videos?id='.rawurlencode($videoId));

        if (! in_array($response->status(), [200, 204], true)) {
            throw new RuntimeException($this->explain($response, 'delete the video'));
        }
    }

    /** Title YouTube accepts: no < or >, at most 100 characters, never empty. */
    public static function cleanTitle(string $title): string
    {
        $title = trim(preg_replace('/\s+/', ' ', str_replace(['<', '>'], '', $title)));

        return mb_substr($title !== '' ? $title : 'Class recording', 0, 100);
    }

    private function explain($response, string $what): string
    {
        $reason = $response->json('error.errors.0.reason') ?? $response->json('error.status');

        return match (true) {
            $reason === 'quotaExceeded' || $reason === 'uploadLimitExceeded' => 'YouTube upload limit reached for today. It will work again tomorrow.',
            $reason === 'insufficientPermissions' || $reason === 'ACCESS_TOKEN_SCOPE_INSUFFICIENT' => 'YouTube needs a new permission (to manage playlists). On the YouTube Connection page click "Connect again" and allow it.',
            $reason === 'youtubeSignupRequired' || $reason === 'forbidden' && $response->status() === 403 => 'This Google account cannot upload to YouTube. Check that the channel exists and is verified.',
            in_array($response->status(), [401], true) => 'The YouTube sign-in was refused. Connect YouTube again.',
            default => "YouTube could not {$what} (error {$response->status()}".($reason ? ", {$reason}" : '').').',
        };
    }
}

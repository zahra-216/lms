<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Services\YoutubePlaylistResolver;
use App\Services\YoutubeService;
use App\Support\YoutubeSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * "YouTube Connection": the admin links the campus YouTube channel (Google sign-in once) so class recordings
 * can be uploaded to it. Keys are saved encrypted and never shown again.
 */
class YoutubeConnectionController extends Controller
{
    private function redirectUri(): string
    {
        return route('admin.youtube.callback');
    }

    public function show()
    {
        return view('admin.youtube.connection', [
            'clientId' => (string) YoutubeSettings::get('client_id'),
            'hasSecret' => (bool) YoutubeSettings::get('client_secret'),
            'hasKeys' => YoutubeSettings::hasKeys(),
            'connected' => YoutubeSettings::isConnected(),
            'channel' => YoutubeSettings::get('channel_title'),
            'redirectUri' => $this->redirectUri(),
            'canManage' => YoutubeSettings::canManagePlaylists(),
            'programmes' => YoutubeSettings::isConnected() ? YoutubePlaylistController::programmes() : collect(),
            'test' => session('youtube_test'),
        ]);
    }

    public function save(Request $request)
    {
        $data = $request->validate([
            'client_id' => ['nullable', 'string', 'max:200', 'regex:/^\S+$/'],
            'client_secret' => ['nullable', 'string', 'max:200', 'regex:/^\S+$/'],
        ], [
            'client_id.regex' => 'The Client ID must not contain spaces - copy it again from Google.',
            'client_secret.regex' => 'The Client Secret must not contain spaces - copy it again from Google.',
        ]);

        $changed = false;
        foreach (['client_id', 'client_secret'] as $key) {
            $value = trim((string) ($data[$key] ?? ''));

            if ($value !== '' && $value !== (string) YoutubeSettings::get($key)) {
                YoutubeSettings::put($key, $value);
                $changed = true;
            }
        }

        if (! $changed) {
            return redirect()->route('admin.youtube.show')->with('success', 'Nothing was changed.');
        }

        // The sign-in belongs to the old keys, so it has to be done again.
        $wasConnected = YoutubeSettings::isConnected();
        YoutubeSettings::disconnect();

        return redirect()->route('admin.youtube.show')->with('success', $wasConnected
            ? 'Keys saved. Because the keys changed, click "Connect YouTube" again.'
            : 'Keys saved. Now click "Connect YouTube".');
    }

    /** Send the admin to Google to allow the LMS to upload to the campus channel. */
    public function connect(Request $request, YoutubeService $youtube)
    {
        try {
            $state = Str::random(40);
            $request->session()->put('youtube_state', $state);

            return redirect()->away($youtube->authUrl($this->redirectUri(), $state));
        } catch (\RuntimeException $e) {
            return redirect()->route('admin.youtube.show')->with('error', $e->getMessage());
        }
    }

    /** Google sends the admin back here. */
    public function callback(Request $request, YoutubeService $youtube)
    {
        $expected = (string) $request->session()->pull('youtube_state');

        if ($request->filled('error')) {
            $why = $request->query('error') === 'access_denied' ? 'You did not allow access, so YouTube was not connected.' : 'Google stopped the sign-in ('.e($request->query('error')).').';

            return redirect()->route('admin.youtube.show')->with('error', $why);
        }

        if ($expected === '' || ! hash_equals($expected, (string) $request->query('state')) || ! $request->filled('code')) {
            return redirect()->route('admin.youtube.show')->with('error', 'The sign-in could not be checked. Please click "Connect YouTube" again.');
        }

        try {
            $channel = $youtube->connect((string) $request->query('code'), $this->redirectUri());
        } catch (\RuntimeException $e) {
            return redirect()->route('admin.youtube.show')->with('error', $e->getMessage());
        }

        return redirect()->route('admin.youtube.show')->with('success', 'YouTube connected to the channel "'.$channel.'".');
    }

    public function disconnect()
    {
        YoutubeSettings::disconnect();

        return redirect()->route('admin.youtube.show')->with('success', 'YouTube was disconnected. Recordings will not be uploaded until you connect it again.');
    }

    /** Upload a small video the admin chooses, to prove the connection works. It goes up as Unlisted. */
    public function test(Request $request, YoutubeService $youtube, YoutubePlaylistResolver $playlists)
    {
        $request->validate([
            'video' => ['required', 'file', 'mimes:mp4,mov,webm,avi,mkv', 'max:51200'],
            'playlist_level' => ['nullable', 'integer'],
        ], [
            'video.required' => 'Choose a short video file first.',
            'video.mimes' => 'Choose a video file (MP4, MOV, WEBM, AVI or MKV).',
            'video.max' => 'Choose a smaller video (50 MB at most) - a few seconds is enough.',
            'video.uploaded' => 'The video could not be uploaded to the LMS. Choose a smaller one.',
        ]);

        @set_time_limit(300);
        $file = $request->file('video');
        $mime = $file->getMimeType() ?: 'video/mp4';

        try {
            $session = $youtube->startUpload($file->getRealPath(), 'TTMC LMS test - you can delete this', 'Test upload from the LMS. Safe to delete.', 'unlisted', $mime);
            $id = $youtube->sendFile($session, $file->getRealPath(), $mime);
        } catch (\Throwable $e) {
            return redirect()->route('admin.youtube.show')->with('youtube_test', ['ok' => false, 'message' => $e->getMessage()]);
        }

        $message = 'The test video is on YouTube (Unlisted). YouTube may need a few minutes to process it.';

        // Optionally try the playlist step too, exactly as a real recording would use it.
        if ($request->filled('playlist_level')) {
            $level = Level::active()->with('course')->find((int) $request->input('playlist_level'));

            if ($level && $level->course) {
                try {
                    $playlist = $playlists->addVideo($level->course, $level, $id);
                    $message .= ' It was added to the playlist "'.$playlist['title'].'"'.($playlist['created'] ? ' (the playlist did not exist, so it was created).' : '.');
                } catch (\Throwable $e) {
                    $message .= ' But it could not be added to the playlist: '.$e->getMessage();
                }
            }
        }

        $request->session()->put('youtube_test_video', $id);

        return redirect()->route('admin.youtube.show')->with('youtube_test', [
            'ok' => true,
            'message' => $message,
            'url' => 'https://youtu.be/'.$id,
            'video_id' => $id,
        ]);
    }

    /** Remove the test video again (only the one that was just uploaded here). */
    public function deleteTest(Request $request, YoutubeService $youtube)
    {
        $id = (string) $request->session()->pull('youtube_test_video');

        if ($id === '' || ! hash_equals($id, (string) $request->input('video_id'))) {
            return redirect()->route('admin.youtube.show')->with('error', 'That test video can no longer be deleted here. Delete it in YouTube Studio.');
        }

        try {
            $youtube->deleteVideo($id);
        } catch (\Throwable $e) {
            return redirect()->route('admin.youtube.show')->with('youtube_test', ['ok' => false, 'message' => 'The test video could not be deleted: '.$e->getMessage()]);
        }

        return redirect()->route('admin.youtube.show')->with('success', 'The test video was deleted from YouTube.');
    }
}

<?php

namespace App\Services;

use App\Support\ZoomSettings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Talks to Zoom for the LMS: create / delete a meeting and read who joined.
 *
 * With ZOOM_FAKE=true (local testing only) nothing leaves this computer: meetings get made-up
 * numbers and the "participants" are read from storage/app/zoom-fake/<meeting id>.json.
 */
class ZoomService
{
    private const API = 'https://api.zoom.us/v2';

    /**
     * The meeting number from what an admin pastes: a Zoom link (https://us02web.zoom.us/j/81234567890?pwd=...)
     * or the number itself, with or without spaces ("812 3456 7890"). Null when it is neither.
     */
    public static function meetingIdFrom(string $text): ?string
    {
        $text = trim($text);

        if (preg_match('~/(?:j|wc/join|wc|s|w)/(\d{9,11})(?:\D|$)~', $text, $m)) {
            return $m[1];
        }

        if (preg_match('/^[\d\s\-]+$/', $text)) {
            $digits = preg_replace('/\D/', '', $text);

            return strlen($digits) >= 9 && strlen($digits) <= 11 ? $digits : null;
        }

        return null;
    }

    public function isFake(): bool
    {
        return (bool) config('services.zoom.fake');
    }

    public function isConfigured(): bool
    {
        return $this->isFake()
            || (ZoomSettings::get('account_id') && ZoomSettings::get('client_id') && ZoomSettings::get('client_secret'));
    }

    /**
     * Check the saved Zoom connection: sign in with the keys, then look for the host email in that Zoom account.
     * Used by the "Test connection" button. Never throws.
     *
     * @return array{ok: bool, message: string, notes: array<int, string>}
     */
    public function testConnection(): array
    {
        if ($this->isFake()) {
            return ['ok' => true, 'message' => 'Testing mode: Zoom is pretended, nothing was checked with the real Zoom.', 'notes' => []];
        }

        if (! (ZoomSettings::get('account_id') && ZoomSettings::get('client_id') && ZoomSettings::get('client_secret'))) {
            return ['ok' => false, 'message' => 'Fill in and save the Account ID, Client ID and Client Secret first.', 'notes' => []];
        }

        Cache::forget(ZoomSettings::tokenCacheKey());

        try {
            $this->token();
        } catch (\Throwable $e) {
            Cache::forget(ZoomSettings::tokenCacheKey());

            return ['ok' => false, 'message' => 'Zoom did not accept these keys. Check the Account ID, Client ID and Client Secret, and that the app is activated in Zoom. ('.$e->getMessage().')', 'notes' => []];
        }

        $notes = [];
        $host = ZoomSettings::get('host');

        if (! $host) {
            $notes[] = 'The Zoom host email is not set yet. Classes cannot be created without it.';
        } else {
            $response = $this->api()->get(self::API.'/users/'.rawurlencode($host));

            if ($response->successful()) {
                $name = trim(($response->json('first_name') ?? '').' '.($response->json('last_name') ?? ''));
                $notes[] = 'Host found in this Zoom account: '.$host.($name !== '' ? " ({$name})" : '').'.';
            } elseif ($response->status() === 404) {
                return ['ok' => false, 'message' => "The keys work, but {$host} is not a user of this Zoom account. Use the email you sign in to Zoom with.", 'notes' => []];
            } else {
                $notes[] = 'The host email could not be checked (the Zoom permission "View a user" is not added to the app). This is fine; the first class will show if it works.';
            }
        }

        return ['ok' => true, 'message' => 'Connected to Zoom. The keys are accepted.', 'notes' => $notes];
    }

    /**
     * Make a real meeting in the campus Zoom account.
     *
     * @return array{id: string, join_url: string, passcode: ?string}
     */
    public function createMeeting(string $topic, Carbon $start, int $durationMinutes): array
    {
        if ($this->isFake()) {
            $id = (string) random_int(80000000000, 89999999999);

            return ['id' => $id, 'join_url' => "https://zoom.us/j/{$id}?pwd=test", 'passcode' => 'test'];
        }

        $timezone = config('services.zoom.timezone');

        // A Server-to-Server app has no "current user", so the Zoom login (email) that owns the meetings must be given.
        if (! ZoomSettings::get('host')) {
            throw new RuntimeException('The Zoom host email is missing. Add it on the Zoom Connection page.');
        }

        $host = rawurlencode(ZoomSettings::get('host'));

        $response = $this->api()->post(self::API."/users/{$host}/meetings", [
            'topic' => $topic,
            'type' => 2,   // a scheduled meeting
            'start_time' => $start->copy()->setTimezone($timezone)->format('Y-m-d\TH:i:s'),
            'timezone' => $timezone,
            'duration' => $durationMinutes,
        ]);

        $this->check($response, 'create the meeting');
        $body = $response->json();

        return [
            'id' => (string) $body['id'],
            'join_url' => $body['join_url'],
            'passcode' => $body['password'] ?? null,
        ];
    }

    /**
     * Title, start time and length of a meeting that already exists in Zoom (used to link a meeting made in the
     * Zoom app). Null when Zoom has no such meeting, or it has no fixed start time (e.g. an instant meeting).
     *
     * @return array{topic: string, start: Carbon, duration: int}|null
     */
    public function meetingDetails(string $meetingId): ?array
    {
        if ($this->isFake()) {
            $file = storage_path("app/zoom-fake/{$meetingId}.meeting.json");
            $d = File::exists($file) ? json_decode(File::get($file), true) : null;

            return $d ? $this->details($d, $meetingId) : null;
        }

        $id = rawurlencode($meetingId);

        // A meeting that has not happened yet, then one that already has.
        foreach (["/meetings/{$id}", "/past_meetings/{$id}"] as $path) {
            $response = $this->api()->get(self::API.$path);

            if ($response->status() === 404) {
                continue;
            }

            $this->check($response, 'read the meeting');

            return $this->details($response->json(), $meetingId);
        }

        return null;
    }

    private function details(array $d, string $meetingId): ?array
    {
        if (empty($d['start_time'])) {
            return null;   // no fixed start time: nothing to hang attendance on
        }

        return [
            'topic' => trim((string) ($d['topic'] ?? '')) ?: "Zoom meeting {$meetingId}",
            'start' => Carbon::parse($d['start_time'])->setTimezone(config('app.timezone')),
            'duration' => max(1, (int) ($d['duration'] ?? 60)),
        ];
    }

    /** Change the title, time or length of a meeting that has not started. The join link stays the same. */
    public function updateMeeting(string $meetingId, string $topic, Carbon $start, int $durationMinutes): void
    {
        if ($this->isFake()) {
            return;
        }

        $timezone = config('services.zoom.timezone');

        $response = $this->api()->patch(self::API.'/meetings/'.rawurlencode($meetingId), [
            'topic' => $topic,
            'type' => 2,
            'start_time' => $start->copy()->setTimezone($timezone)->format('Y-m-d\TH:i:s'),
            'timezone' => $timezone,
            'duration' => $durationMinutes,
        ]);

        $this->check($response, 'update the meeting');
    }

    public function deleteMeeting(string $meetingId): void
    {
        if ($this->isFake()) {
            return;
        }

        $response = $this->api()->delete(self::API.'/meetings/'.rawurlencode($meetingId));

        // 404 means it is already gone in Zoom, which is what we want.
        if ($response->status() !== 404) {
            $this->check($response, 'delete the meeting');
        }
    }

    /**
     * Everyone Zoom saw in the meeting (one entry per time they joined).
     *
     * @return array<int, array{name: string, email: ?string, join_time: ?Carbon, leave_time: ?Carbon, duration: int}>
     */
    public function participants(string $meetingId, ?string $uuid = null): array
    {
        if ($this->isFake()) {
            $file = storage_path("app/zoom-fake/{$meetingId}.json");
            $rows = File::exists($file) ? json_decode(File::get($file), true) : [];

            return array_map(fn ($r) => $this->row($r), $rows ?: []);
        }

        // Zoom wants the UUID when we have it (a recurring ID has many past meetings).
        // A UUID that starts with "/" or contains "//" must be url-encoded twice.
        $key = $meetingId;
        if ($uuid) {
            $key = (str_starts_with($uuid, '/') || str_contains($uuid, '//')) ? rawurlencode(rawurlencode($uuid)) : rawurlencode($uuid);
        }

        $all = [];
        $token = null;

        do {
            $query = ['page_size' => 300];
            if ($token) {
                $query['next_page_token'] = $token;
            }

            $response = $this->api()->get(self::API."/report/meetings/{$key}/participants", $query);

            // 404 = Zoom has not prepared the report yet (or the meeting never happened): not an error, just "no list yet".
            if ($response->status() === 404) {
                return [];
            }

            $this->check($response, 'read the participants');

            foreach ($response->json('participants') ?? [] as $p) {
                $all[] = $this->row($p);
            }

            $token = $response->json('next_page_token');
        } while ($token);

        return $all;
    }

    private function row(array $p): array
    {
        return [
            'name' => trim((string) ($p['name'] ?? '')),
            'email' => ($p['user_email'] ?? $p['email'] ?? '') ?: null,
            'join_time' => ! empty($p['join_time']) ? Carbon::parse($p['join_time'])->setTimezone(config('app.timezone')) : null,
            'leave_time' => ! empty($p['leave_time']) ? Carbon::parse($p['leave_time'])->setTimezone(config('app.timezone')) : null,
            'duration' => (int) ($p['duration'] ?? 0),
        ];
    }

    private function api()
    {
        return Http::withToken($this->token())->acceptJson()->timeout(30);
    }

    /** Zoom gives a key that works for one hour; keep it for 55 minutes. */
    private function token(): string
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Zoom is not connected yet. Add the Zoom keys on the Zoom Connection page.');
        }

        return Cache::remember(ZoomSettings::tokenCacheKey(), now()->addMinutes(55), function () {
            // Exactly as Zoom documents it: the two values go in the address, the keys in Basic auth.
            $url = 'https://zoom.us/oauth/token?'.http_build_query([
                'grant_type' => 'account_credentials',
                'account_id' => ZoomSettings::get('account_id'),
            ]);

            $response = Http::withBasicAuth(ZoomSettings::get('client_id'), ZoomSettings::get('client_secret'))
                ->timeout(30)
                ->send('POST', $url);

            $this->check($response, 'sign in to Zoom');

            return $response->json('access_token');
        });
    }

    private function check($response, string $what): void
    {
        if ($response->successful()) {
            return;
        }

        // A key Zoom no longer accepts: forget it so the next call signs in again.
        if ($response->status() === 401) {
            Cache::forget(ZoomSettings::tokenCacheKey());
        }

        $message = $response->json('message') ?: $response->body();

        throw new RuntimeException("Zoom could not {$what} (error {$response->status()}): {$message}");
    }
}

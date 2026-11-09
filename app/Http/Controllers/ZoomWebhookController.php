<?php

namespace App\Http\Controllers;

use App\Models\ZoomMeeting;
use App\Services\ZoomAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Zoom calls this address. Two things matter:
 *  - endpoint.url_validation: Zoom checks that the address is ours when the app is set up.
 *  - meeting.ended: a class finished, so fetch who joined.
 * Every request is checked against the app's secret token, so nobody else can trigger it.
 */
class ZoomWebhookController extends Controller
{
    public function handle(Request $request, ZoomAttendance $attendance)
    {
        $secret = (string) config('services.zoom.webhook_secret');

        if ($secret === '') {
            return response()->json(['message' => 'Zoom webhook is not set up.'], 503);
        }

        $event = $request->input('event');

        if ($event === 'endpoint.url_validation') {
            $plain = (string) $request->input('payload.plainToken');

            // Zoom's plain token is random letters and digits. Refuse anything else so this answer can
            // never be used to produce a valid signature for a made-up request.
            if ($plain === '' || ! preg_match('/^[A-Za-z0-9_-]+$/', $plain)) {
                return response()->json(['message' => 'Bad token.'], 400);
            }

            return response()->json([
                'plainToken' => $plain,
                'encryptedToken' => hash_hmac('sha256', $plain, $secret),
            ]);
        }

        if (! $this->signatureIsValid($request, $secret)) {
            return response()->json(['message' => 'Invalid signature.'], 401);
        }

        if ($event === 'meeting.ended') {
            $object = $request->input('payload.object', []);
            $meeting = ZoomMeeting::where('zoom_meeting_id', (string) ($object['id'] ?? ''))->first();

            // Not one of ours (e.g. a meeting made directly in Zoom): nothing to do.
            if ($meeting && $meeting->status !== 'processed') {
                $meeting->update([
                    'zoom_uuid' => $object['uuid'] ?? $meeting->zoom_uuid,
                    'status' => 'ended',
                    'ended_at' => now(),
                    'attempts' => 0,
                    'next_try_at' => now()->addMinutes(config('services.zoom.retry_minutes')),
                ]);

                // Reply to Zoom first, then fetch. If Zoom's list is not ready yet, the scheduled
                // command (zoom:sync) keeps trying.
                $id = $meeting->id;
                app()->terminating(function () use ($id, $attendance) {
                    try {
                        $attendance->run(ZoomMeeting::findOrFail($id));
                    } catch (Throwable $e) {
                        Log::error("Zoom attendance failed for class {$id}: {$e->getMessage()}");
                    }
                });
            }
        }

        return response()->json(['ok' => true]);
    }

    /** Zoom signs "v0:<timestamp>:<body>" with the secret token (HMAC SHA-256). */
    private function signatureIsValid(Request $request, string $secret): bool
    {
        $timestamp = (string) $request->header('x-zm-request-timestamp');
        $signature = (string) $request->header('x-zm-signature');

        if ($timestamp === '' || $signature === '' || abs(time() - (int) $timestamp) > 300) {
            return false;
        }

        $expected = 'v0='.hash_hmac('sha256', "v0:{$timestamp}:".$request->getContent(), $secret);

        return hash_equals($expected, $signature);
    }
}

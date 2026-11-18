<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ZoomMeeting;
use App\Services\ZoomService;
use App\Support\ZoomSettings;
use Illuminate\Http\Request;

/**
 * "Zoom Connection": lets the admin change which Zoom account the LMS uses, without a developer.
 * Secrets are saved encrypted and never shown again; a blank secret box keeps the saved one.
 */
class ZoomConnectionController extends Controller
{
    public function show(ZoomService $zoom)
    {
        $fields = [];
        foreach (ZoomSettings::FIELDS as $key => [$config, $secret]) {
            $fields[$key] = [
                'source' => ZoomSettings::source($key),
                'secret' => $secret,
                'value' => $secret ? '' : (string) ZoomSettings::get($key),
            ];
        }

        return view('admin.zoom.connection', [
            'fields' => $fields,
            'connected' => $zoom->isConfigured(),
            'fake' => $zoom->isFake(),
            'webhookUrl' => url('/zoom/webhook'),
            'pending' => ZoomMeeting::where('status', '!=', 'processed')->count(),
            'test' => session('zoom_test'),
        ]);
    }

    public function save(Request $request)
    {
        $id = ['nullable', 'string', 'max:120', 'regex:/^[A-Za-z0-9_.\-]+$/'];
        $secret = ['nullable', 'string', 'max:200', 'regex:/^\S+$/'];

        $data = $request->validate([
            'account_id' => $id,
            'client_id' => $id,
            'client_secret' => $secret,
            'webhook_secret' => $secret,
            'host' => ['nullable', 'email', 'max:190'],
        ], [
            'account_id.regex' => 'The Account ID has letters, numbers, dashes and underscores only - copy it again from Zoom.',
            'client_id.regex' => 'The Client ID has letters, numbers, dashes and underscores only - copy it again from Zoom.',
            'client_secret.regex' => 'The Client Secret must not contain spaces - copy it again from Zoom.',
            'webhook_secret.regex' => 'The Secret Token must not contain spaces - copy it again from Zoom.',
            'host.email' => 'Enter the Zoom login email, for example name@example.com.',
        ]);

        $changed = [];
        foreach (array_keys(ZoomSettings::FIELDS) as $key) {
            $value = trim((string) ($data[$key] ?? ''));

            // An empty box keeps what is saved already.
            if ($value !== '' && $value !== (string) ZoomSettings::get($key)) {
                ZoomSettings::put($key, $value);
                $changed[] = $key;
            }
        }

        if (! $changed) {
            return redirect()->route('admin.zoom-connection.show')->with('success', 'Nothing was changed.');
        }

        return redirect()->route('admin.zoom-connection.show')
            ->with('success', 'Zoom connection saved. Press "Test connection" to check it.');
    }

    public function test(ZoomService $zoom)
    {
        return redirect()->route('admin.zoom-connection.show')->with('zoom_test', $zoom->testConnection());
    }
}

<?php

namespace App\Support;

use App\Models\AppSetting;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;

/**
 * The Zoom connection (which Zoom account the LMS uses). The admin sets it on the "Zoom Connection" page; it is
 * stored encrypted in the database. When the admin has saved nothing, the values in the server's .env are used
 * (ZOOM_ACCOUNT_ID, ZOOM_CLIENT_ID, ZOOM_CLIENT_SECRET, ZOOM_WEBHOOK_SECRET, ZOOM_HOST_EMAIL), so nothing breaks.
 */
class ZoomSettings
{
    /** key => [env/config name, secret?] */
    public const FIELDS = [
        'account_id' => ['services.zoom.account_id', false],
        'client_id' => ['services.zoom.client_id', false],
        'client_secret' => ['services.zoom.client_secret', true],
        'webhook_secret' => ['services.zoom.webhook_secret', true],
        'host' => ['services.zoom.host', false],
    ];

    private static ?array $saved = null;

    private static function dbKey(string $key): string
    {
        return 'zoom.'.$key;
    }

    /** What the admin saved (decrypted), or null. */
    public static function saved(string $key): ?string
    {
        if (self::$saved === null) {
            self::$saved = [];

            try {
                if (Schema::hasTable('app_settings')) {
                    foreach (AppSetting::where('key', 'like', 'zoom.%')->pluck('value', 'key') as $k => $v) {
                        try {
                            self::$saved[$k] = filled($v) ? Crypt::decryptString($v) : null;
                        } catch (DecryptException) {
                            self::$saved[$k] = null;   // saved with another APP_KEY: treat as not saved
                        }
                    }
                }
            } catch (\Throwable) {
                // database not reachable (e.g. while installing): fall back to .env
            }
        }

        return self::$saved[self::dbKey($key)] ?? null;
    }

    /** The value in use: saved by the admin, else the server .env. */
    public static function get(string $key): ?string
    {
        $value = self::saved($key);

        return filled($value) ? $value : (filled(config(self::FIELDS[$key][0])) ? (string) config(self::FIELDS[$key][0]) : null);
    }

    /** "admin" (saved on the page), "server" (.env) or "none". */
    public static function source(string $key): string
    {
        return filled(self::saved($key)) ? 'admin' : (filled(config(self::FIELDS[$key][0])) ? 'server' : 'none');
    }

    public static function put(string $key, ?string $value): void
    {
        AppSetting::updateOrCreate(['key' => self::dbKey($key)], ['value' => filled($value) ? Crypt::encryptString($value) : null]);
        self::$saved = null;
        Cache::forget('zoom.access_token');
    }

    /** Back to the server .env values. */
    public static function clear(): void
    {
        AppSetting::where('key', 'like', 'zoom.%')->delete();
        self::$saved = null;
        Cache::forget('zoom.access_token');
    }

    /** A key made from the account details, so a new account never reuses the old account's token. */
    public static function tokenCacheKey(): string
    {
        return 'zoom.access_token.'.md5((string) self::get('account_id').'|'.(string) self::get('client_id'));
    }
}

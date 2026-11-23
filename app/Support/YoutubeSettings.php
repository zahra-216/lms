<?php

namespace App\Support;

use App\Models\AppSetting;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;

/**
 * The campus YouTube connection (which channel the LMS uploads class recordings to). Set on the admin
 * "YouTube Connection" page and stored encrypted in the database (table app_settings, keys youtube.*).
 */
class YoutubeSettings
{
    public const KEYS = ['client_id', 'client_secret', 'refresh_token', 'channel_title', 'channel_id', 'scopes'];

    private static ?array $saved = null;

    private static function dbKey(string $key): string
    {
        return 'youtube.'.$key;
    }

    public static function get(string $key): ?string
    {
        if (self::$saved === null) {
            self::$saved = [];

            try {
                if (Schema::hasTable('app_settings')) {
                    foreach (AppSetting::where('key', 'like', 'youtube.%')->pluck('value', 'key') as $k => $v) {
                        try {
                            self::$saved[$k] = filled($v) ? Crypt::decryptString($v) : null;
                        } catch (DecryptException) {
                            self::$saved[$k] = null;
                        }
                    }
                }
            } catch (\Throwable) {
                // database not reachable: behave as "not connected"
            }
        }

        $value = self::$saved[self::dbKey($key)] ?? null;

        return filled($value) ? $value : null;
    }

    public static function put(string $key, ?string $value): void
    {
        AppSetting::updateOrCreate(['key' => self::dbKey($key)], ['value' => filled($value) ? Crypt::encryptString($value) : null]);
        self::$saved = null;
        Cache::forget('youtube.access_token');
    }

    /** Forget the connected channel (keeps the Google keys so the admin can connect again). */
    public static function disconnect(): void
    {
        foreach (['refresh_token', 'channel_title', 'channel_id', 'scopes'] as $key) {
            AppSetting::where('key', self::dbKey($key))->delete();
        }
        self::$saved = null;
        Cache::forget('youtube.access_token');
    }

    public static function hasKeys(): bool
    {
        return (bool) (self::get('client_id') && self::get('client_secret'));
    }

    /** Was the permission to manage playlists given when the channel was connected? (The first version only asked for uploads.) */
    public static function canManagePlaylists(): bool
    {
        return in_array('https://www.googleapis.com/auth/youtube', preg_split('/\s+/', (string) self::get('scopes')), true);
    }

    public static function isConnected(): bool
    {
        return self::hasKeys() && (bool) self::get('refresh_token');
    }
}

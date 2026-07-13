<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

#[Fillable(['key', 'value'])]
class Setting extends Model
{
    /** @var list<string> */
    private static array $encryptedKeys = [
        'mail.password',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::query()->where('key', $key)->first();

        if (! $setting || $setting->value === null) {
            return $default;
        }

        if (in_array($key, self::$encryptedKeys, true)) {
            try {
                return Crypt::decryptString($setting->value);
            } catch (\Throwable) {
                return $default;
            }
        }

        return $setting->value;
    }

    public static function set(string $key, mixed $value): void
    {
        if ($value === null || $value === '') {
            static::query()->where('key', $key)->delete();

            return;
        }

        $storedValue = in_array($key, self::$encryptedKeys, true)
            ? Crypt::encryptString((string) $value)
            : (string) $value;

        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $storedValue],
        );
    }

    public static function has(string $key): bool
    {
        return static::query()->where('key', $key)->exists();
    }
}

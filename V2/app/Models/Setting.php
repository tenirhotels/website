<?php

namespace App\Models;

use App\Support\Locales;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Настройки сайта: ключ → значение (JSON).
 * Setting::get('contacts.phone'), Setting::text('seo.default_title') — переводимое значение.
 */
class Setting extends Model
{
    public const CACHE_KEY = 'settings.all';

    protected $guarded = ['id'];

    protected $casts = ['value' => 'array'];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }

    public static function values(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            try {
                return self::query()->pluck('value', 'key')
                    ->map(fn ($v) => is_string($v) ? json_decode($v, true) : $v)
                    ->all();
            } catch (\Throwable) {
                return []; // до миграций
            }
        });
    }

    /** Точечная нотация: group.key */
    public static function get(string $key, mixed $default = null): mixed
    {
        [$group, $rest] = array_pad(explode('.', $key, 2), 2, null);
        $value = self::values()[$group] ?? null;

        return $rest === null ? ($value ?? $default) : data_get($value, $rest, $default);
    }

    /** Переводимое значение {"ru":..., "en":...} с fallback. */
    public static function text(string $key, ?string $default = null): ?string
    {
        $value = self::get($key);

        return is_array($value) ? (Locales::pick($value) ?? $default) : ($value ?? $default);
    }

    public static function put(string $group, array $value): void
    {
        self::query()->updateOrCreate(['key' => $group], ['value' => $value]);
    }
}

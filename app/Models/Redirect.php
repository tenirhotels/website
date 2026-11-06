<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Redirect extends Model
{
    public const CACHE_KEY = 'redirects.map';

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
        'last_hit_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (Redirect $redirect) {
            $redirect->from_path = self::normalize($redirect->from_path);
        });
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }

    /** /EN-GB/Rooms/ → /en-gb/rooms */
    public static function normalize(string $path): string
    {
        $path = '/'.trim(parse_url($path, PHP_URL_PATH) ?? '', '/');

        return mb_strtolower($path);
    }

    /** @return array<string, array{id:int,to:string,code:int}> */
    public static function map(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, fn () => self::query()
            ->where('is_active', true)
            ->get(['id', 'from_path', 'to_path', 'status_code'])
            ->mapWithKeys(fn ($r) => [$r->from_path => ['id' => $r->id, 'to' => $r->to_path, 'code' => $r->status_code]])
            ->all());
    }
}

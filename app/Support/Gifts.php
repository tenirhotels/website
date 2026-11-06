<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Подарочные сертификаты из gifter (gifts.tenirhotels.com): список с ценами берём из его публичного JSON /api/offers.
 * Ответ хранится в кэше; если gifter недоступен — показываем последнюю удачную копию, а не ошибку.
 * Продажа и оплата остаются в gifter: карточка на сайте ведёт на страницу заказа.
 */
class Gifts
{
    private const KEY = 'gifts.offers';

    /** Языки gifter; для остальных (zh) берём английский. */
    private const LANGS = ['kk', 'ru', 'en'];

    public static function baseUrl(): string
    {
        return rtrim((string) (Setting::get('gifts.url') ?: config('tenir.gifts.url')), '/');
    }

    public static function enabled(): bool
    {
        return self::baseUrl() !== '' && Setting::get('gifts.enabled', true) !== false;
    }

    /** Язык витрины gifter для языка сайта. */
    public static function lang(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return in_array($locale, self::LANGS, true) ? $locale : 'en';
    }

    /**
     * Открытые к продаже сертификаты на языке сайта.
     *
     * @return list<array{id: int, name: string, description: string, validity: string, price: float, currency: string, image: ?string, url: string}>
     */
    public static function offers(?string $locale = null): array
    {
        if (! self::enabled()) {
            return [];
        }

        $lang = self::lang($locale);
        $stored = Cache::get(self::KEY);
        if (! is_array($stored) || (time() - ($stored['at'] ?? 0)) > (int) config('tenir.gifts.ttl')) {
            $stored = self::sync() ?? $stored;
        }

        return collect($stored['offers'] ?? [])
            ->filter(fn ($o) => ($o['open'] ?? false) && ($o['price'] ?? 0) > 0)
            ->map(fn (array $o) => [
                'id' => (int) $o['id'],
                'name' => self::pick($o['name'] ?? [], $lang),
                'description' => self::pick($o['description'] ?? [], $lang),
                'validity' => self::pick($o['validity'] ?? [], $lang),
                'price' => (float) $o['price'],
                'currency' => $o['currency'] ?? 'KZT',
                'image' => $o['image'] ? $o['image'].(str_contains($o['image'], '?') ? '&' : '?').'w=720' : null,
                'url' => $o['url'].(str_contains($o['url'], '?') ? '&' : '?').'lang='.$lang,
            ])->values()->all();
    }

    /**
     * Забирает свежий список из gifter. Если он изменился — сбрасывает кэш страниц сайта.
     * Возвращает сохранённую запись или null, если gifter не ответил (тогда остаётся прежняя копия).
     *
     * @return array{at: int, hash: string, offers: list<array<string, mixed>>}|null
     */
    public static function sync(): ?array
    {
        if (! self::enabled()) {
            return null;
        }

        try {
            $response = Http::timeout(5)->acceptJson()->get(self::baseUrl().'/api/offers');
            $offers = $response->successful() ? $response->json('offers') : null;
        } catch (\Throwable $e) {
            Log::warning('Gifts: gifter недоступен — '.$e->getMessage());

            return null;
        }
        if (! is_array($offers)) {
            return null;
        }

        $hash = md5(json_encode($offers));
        $previous = Cache::get(self::KEY);
        $record = ['at' => time(), 'hash' => $hash, 'offers' => $offers];
        Cache::forever(self::KEY, $record);
        if (($previous['hash'] ?? null) !== $hash) {
            PageCache::flush();
        }

        return $record;
    }

    /** @param  array<string, string>  $byLang */
    private static function pick(array $byLang, string $lang): string
    {
        foreach ([$lang, 'ru', 'en', 'kk'] as $code) {
            if (filled($byLang[$code] ?? null)) {
                return (string) $byLang[$code];
            }
        }

        return '';
    }
}

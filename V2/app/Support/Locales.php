<?php

namespace App\Support;

class Locales
{
    /** @return array<string, array> */
    public static function all(): array
    {
        return config('tenir.locales');
    }

    /** @return list<string> */
    public static function codes(): array
    {
        return array_keys(self::all());
    }

    public static function default(): string
    {
        return config('tenir.default_locale');
    }

    public static function isSupported(?string $code): bool
    {
        return $code !== null && array_key_exists($code, self::all());
    }

    /** Цепочка языков для поиска перевода: kk → ru, zh → en → ru. */
    public static function chain(string $code): array
    {
        $chain = [];
        while ($code && ! in_array($code, $chain, true) && self::isSupported($code)) {
            $chain[] = $code;
            $code = self::all()[$code]['fallback'] ?? null;
        }
        if (! in_array(self::default(), $chain, true)) {
            $chain[] = self::default();
        }

        return $chain;
    }

    /** Лучший язык по заголовку Accept-Language браузера. */
    public static function fromBrowser(?string $header): string
    {
        foreach (explode(',', (string) $header) as $part) {
            $code = strtolower(substr(trim(explode(';', $part)[0]), 0, 2));
            if (self::isSupported($code)) {
                return $code;
            }
        }

        return self::default();
    }

    /** Подпись из массива вида ['ru' => ..., 'en' => ...] с fallback. */
    public static function pick(array $labels, ?string $locale = null): ?string
    {
        foreach (self::chain($locale ?? app()->getLocale()) as $code) {
            if (filled($labels[$code] ?? null)) {
                return $labels[$code];
            }
        }

        return reset($labels) ?: null;
    }
}

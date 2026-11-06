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
        foreach ([self::source(), self::default()] as $code) {
            if (! in_array($code, $chain, true)) {
                $chain[] = $code;
            }
        }

        return $chain;
    }

    public static function source(): string
    {
        return config('tenir.source_locale', self::default());
    }

    /**
     * Язык по заголовку Accept-Language с учётом q-весов: ru-RU,ru;q=0.9,en;q=0.8.
     * Не поддерживаемый язык → основной (казахский).
     */
    public static function fromBrowser(?string $header): string
    {
        $found = [];
        foreach (explode(',', (string) $header) as $i => $part) {
            $bits = explode(';', trim($part));
            $code = strtolower(substr(trim($bits[0]), 0, 2));
            $q = 1.0;
            foreach (array_slice($bits, 1) as $param) {
                if (preg_match('/^\s*q=([0-9.]+)/i', $param, $m)) {
                    $q = (float) $m[1];
                }
            }
            $code = config('tenir.locale_aliases.'.$code, $code);
            if ($q > 0 && self::isSupported($code)) {
                $found[] = [$q, -$i, $code];
            }
        }
        if ($found) {
            rsort($found);

            return $found[0][2];
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

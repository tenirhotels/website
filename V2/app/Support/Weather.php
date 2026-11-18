<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Погода по координатам и высоте — Open-Meteo (бесплатно, без ключа). Ответ кэшируется на 30 минут;
 * если сервис не отвечает, отдаётся последний удачный ответ (до 12 часов), иначе null.
 */
class Weather
{
    private const FRESH = 1800;
    private const STALE = 43200;

    public static function for(float $lat, float $lng, int $altitude): ?array
    {
        $key = sprintf('weather:%.3f:%.3f:%d', $lat, $lng, $altitude);

        if (is_array($hit = Cache::get($key))) {
            return $hit;
        }

        try {
            $res = Http::timeout(5)->retry(1, 200, throw: false)->get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => $lat,
                'longitude' => $lng,
                'elevation' => $altitude,
                'current' => 'temperature_2m,apparent_temperature,weather_code,wind_speed_10m,wind_gusts_10m,relative_humidity_2m,is_day',
                'daily' => 'weather_code,temperature_2m_max,temperature_2m_min,snowfall_sum,precipitation_sum,wind_speed_10m_max',
                'timezone' => 'Asia/Almaty',
                'forecast_days' => 6,
                'wind_speed_unit' => 'ms',
            ]);
            $data = $res->successful() ? self::shape($res->json()) : null;
        } catch (\Throwable) {
            $data = null;
        }

        if ($data) {
            Cache::put($key, $data, self::FRESH);
            Cache::put($key.':stale', $data, self::STALE);

            return $data;
        }

        return Cache::get($key.':stale');
    }

    private static function shape(?array $j): ?array
    {
        $c = $j['current'] ?? null;
        $d = $j['daily'] ?? null;
        if (! $c || ! $d || empty($d['time'])) {
            return null;
        }

        $days = [];
        foreach ($d['time'] as $i => $date) {
            $days[] = [
                'date' => $date,
                'code' => (int) ($d['weather_code'][$i] ?? 0),
                'max' => (int) round($d['temperature_2m_max'][$i] ?? 0),
                'min' => (int) round($d['temperature_2m_min'][$i] ?? 0),
                'snow' => round((float) ($d['snowfall_sum'][$i] ?? 0), 1),
                'wind' => (int) round($d['wind_speed_10m_max'][$i] ?? 0),
            ];
        }

        return [
            'now' => [
                'temp' => (int) round($c['temperature_2m'] ?? 0),
                'feels' => (int) round($c['apparent_temperature'] ?? 0),
                'code' => (int) ($c['weather_code'] ?? 0),
                'wind' => (int) round($c['wind_speed_10m'] ?? 0),
                'gust' => (int) round($c['wind_gusts_10m'] ?? 0),
                'humidity' => (int) ($c['relative_humidity_2m'] ?? 0),
                'day' => (bool) ($c['is_day'] ?? true),
            ],
            'days' => $days,
            'updated' => now('Asia/Almaty')->format('H:i'),
        ];
    }
}

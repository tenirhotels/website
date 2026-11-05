<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * 301-редиректы со старых адресов (V1 и конструктор Exely) по таблице redirects.
 * Параметры запроса сохраняются: /en-gb/booking/?room-type=62361 → /en/booking?room-type=62361
 */
class LegacyRedirects
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->isMethod('GET') || $request->is('admin*', 'livewire*', 'storage/*', 'build/*')) {
            return $next($request);
        }

        $path = Redirect::normalize($request->getPathInfo());
        $match = Redirect::map()[$path] ?? null;

        if (! $match) {
            return $next($request);
        }

        Redirect::query()->whereKey($match['id'])->update([
            'hits' => \DB::raw('hits + 1'),
            'last_hit_at' => now(),
        ]);

        $target = $match['to'];
        if ($query = $request->getQueryString()) {
            $target .= (str_contains($target, '?') ? '&' : '?').$query;
        }

        return redirect($target, $match['code']);
    }
}

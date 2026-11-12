<?php

namespace App\Http\Middleware;

use App\Support\Locales;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * Язык берётся из первого сегмента адреса: /ru/..., /kk/..., /en/..., /zh/...
 * route('...') дальше сам подставляет текущий язык.
 */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale');

        abort_unless(Locales::isSupported($locale), 404);

        app()->setLocale($locale);
        Carbon::setLocale($locale);
        URL::defaults(['locale' => $locale]);

        // Контроллерам параметр языка не нужен.
        $request->route()->forgetParameter('locale');

        return $next($request);
    }
}

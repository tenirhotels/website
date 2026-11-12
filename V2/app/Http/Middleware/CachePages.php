<?php

namespace App\Http\Middleware;

use App\Support\PageCache;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * Отдаёт готовый HTML публичных страниц из кэша: без запросов к БД и рендера шаблонов.
 * Кэшируются только GET без параметров и успешные HTML-ответы без cookie.
 */
class CachePages
{
    private const BROWSER_TTL = 60;   // секунд в браузере
    private const EDGE_TTL = 600;     // секунд на CDN (Cloudflare и т. п.)

    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->isMethod('GET') || $request->query() || $request->routeIs('booking') || ! PageCache::enabled()) {
            return $next($request);
        }

        $key = PageCache::key($request->getSchemeAndHttpHost().$request->getPathInfo());

        if (is_array($hit = Cache::get($key))) {
            return $this->decorate(new Response($hit['html'], 200, ['Content-Type' => 'text/html; charset=UTF-8']), 'HIT');
        }

        $response = $next($request);

        if ($response->getStatusCode() === 200
            && str_contains((string) $response->headers->get('Content-Type'), 'text/html')
            && ! $response->headers->getCookies()) {
            Cache::put($key, ['html' => $response->getContent()], (int) config('tenir.page_cache.ttl'));

            return $this->decorate($response, 'MISS');
        }

        return $response;
    }

    private function decorate(Response $response, string $state): Response
    {
        $response->headers->set('Cache-Control', sprintf('public, max-age=%d, s-maxage=%d, stale-while-revalidate=86400', self::BROWSER_TTL, self::EDGE_TTL));
        $response->headers->set('X-Page-Cache', $state);

        return $response;
    }
}

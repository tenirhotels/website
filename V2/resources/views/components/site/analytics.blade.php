@php
    use App\Models\Setting;

    // Идентификаторы проверяются форматом: в страницу попадает только валидный ID.
    $ga4 = preg_match('/^G-[A-Z0-9]{4,}$/i', (string) Setting::get('analytics.ga4')) ? trim(Setting::get('analytics.ga4')) : null;
    $gtm = preg_match('/^GTM-[A-Z0-9]{4,}$/i', (string) Setting::get('analytics.gtm')) ? trim(Setting::get('analytics.gtm')) : null;
    $ym = preg_match('/^\d{5,}$/', trim((string) Setting::get('analytics.yandex_metrika'))) ? (int) trim(Setting::get('analytics.yandex_metrika')) : null;
    $pixel = preg_match('/^\d{8,}$/', trim((string) Setting::get('analytics.meta_pixel'))) ? trim(Setting::get('analytics.meta_pixel')) : null;
    $webvisor = (bool) Setting::get('analytics.yandex_webvisor', false);
    $immediate = (bool) Setting::get('analytics.load_immediately', false);
@endphp
@if ($ga4 || $gtm || $ym || $pixel)
    {{-- Счётчики подгружаются после первого действия посетителя (скролл, касание, клик) или через 4 с после загрузки — это не тормозит первый экран и PageSpeed. --}}
    <script>
        (function (w, d) {
            var started = false;
            function start() {
                if (started) return;
                started = true;
                @if ($gtm)
                w.dataLayer = w.dataLayer || [];
                w.dataLayer.push({'gtm.start': new Date().getTime(), event: 'gtm.js'});
                var g = d.createElement('script'); g.async = true;
                g.src = 'https://www.googletagmanager.com/gtm.js?id=' + @json($gtm);
                d.head.appendChild(g);
                @endif
                @if ($ga4)
                w.dataLayer = w.dataLayer || [];
                w.gtag = function () { w.dataLayer.push(arguments); };
                w.gtag('js', new Date());
                w.gtag('config', @json($ga4));
                var a = d.createElement('script'); a.async = true;
                a.src = 'https://www.googletagmanager.com/gtag/js?id=' + @json($ga4);
                d.head.appendChild(a);
                @endif
                @if ($ym)
                (function (m, e, t, r, i, k, a) {
                    m[i] = m[i] || function () { (m[i].a = m[i].a || []).push(arguments); };
                    m[i].l = 1 * new Date();
                    k = e.createElement(t); a = e.getElementsByTagName(t)[0]; k.async = 1; k.src = r; a.parentNode.insertBefore(k, a);
                })(w, d, 'script', 'https://mc.yandex.ru/metrika/tag.js', 'ym');
                w.ym({{ $ym }}, 'init', {clickmap: true, trackLinks: true, accurateTrackBounce: true, webvisor: {{ $webvisor ? 'true' : 'false' }}});
                @endif
                @if ($pixel)
                (function (f, b, e, v, n, t, s) {
                    if (f.fbq) return;
                    n = f.fbq = function () { n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments); };
                    if (!f._fbq) f._fbq = n;
                    n.push = n; n.loaded = true; n.version = '2.0'; n.queue = [];
                    t = b.createElement(e); t.async = true; t.src = v;
                    s = b.getElementsByTagName(e)[0]; s.parentNode.insertBefore(t, s);
                })(w, d, 'script', 'https://connect.facebook.net/en_US/fbevents.js');
                w.fbq('init', @json($pixel));
                w.fbq('track', 'PageView');
                @endif
            }
            @if ($immediate)
            start();
            @else
            ['scroll', 'pointerdown', 'keydown', 'touchstart'].forEach(function (e) {
                w.addEventListener(e, start, {once: true, passive: true});
            });
            w.addEventListener('load', function () { setTimeout(start, 4000); });
            @endif
        })(window, document);
    </script>
@endif

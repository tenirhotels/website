@props(['hotel' => null, 'id' => 'bb'])
@php
    // Плашка бронирования на первом экране — виджет Exely (TravelLine) «search-form», как в V1:
    // календарь с ценами на каждую ночь, наличие номеров, гости. Виджет стоит в собственной панели
    // сайта (песочный фон, подпись слева), нижним краем заходит на фото. После выбора дат модуль
    // сам открывает страницу бронирования (адрес задаётся в кабинете Exely → «Форма поиска»).
    $exelyLocale = \App\Support\Locales::all()[app()->getLocale()]['exely'] ?? 'ru';
@endphp
<div class="tenir-search rounded-3xl border border-white/40 bg-white p-2 shadow-[0_20px_50px_rgba(22,33,27,0.22)] md:p-3">
    <div id="tl-search-form" class="tl-container min-w-0 md:min-h-[52px]">
        <noscript><a href="{{ route('booking') }}" class="btn-primary">{{ __('site.booking.search') }}</a></noscript>
    </div>
</div>
<script>
    (function (w) {
        // Форма поиска Exely с прежней интеграцией (серая тема, как на tenirhotels.com).
        // По кнопке «Найти» переходим на нашу страницу бронирования с теми же параметрами.
        var bookingUrl = @json(route('booking'));
        var hotel = @json($hotel?->slug);
        var q = [
            ['setContext', @json(config('tenir.exely.search_context')), @json($exelyLocale)],
            ['embed', 'search-form', {
                providers: [[@json((string) config('tenir.exely.provider'))]],
                container: 'tl-search-form',
                adults: '2',
                nights: 1,
                language: @json($exelyLocale),
                currency: 'USD',
                onsubmit: function (params, url) {
                    var qs = String(url).split('?').pop();
                    return bookingUrl + '?' + qs + (hotel ? '&hotel=' + encodeURIComponent(hotel) : '');
                }
            }]
        ];
        var t = w.bookingengine = (w.bookingengine || {}), ti = t.integration = (t.integration || {});
        ti.__cq = ti.__cq ? ti.__cq.concat(q) : q;
        if (!ti.__loader) {
            ti.__loader = true;
            var d = w.document, s = d.createElement('script');
            s.async = true;
            s.src = @json(config('tenir.exely.reviews_loader'));
            (d.getElementsByTagName('head')[0] || d.getElementsByTagName('body')[0]).appendChild(s);
        }
    })(window);
</script>

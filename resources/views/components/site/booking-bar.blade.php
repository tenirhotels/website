@props(['hotel' => null])
@php
    // Плашка бронирования — виджет Exely (TravelLine) «search-form», как в V1: календарь с ценами
    // на каждую ночь, наличие номеров, гости. Как у Marriott / Six Senses, плашка закреплена
    // под шапкой на всех страницах (кроме самой страницы бронирования), на телефоне вместо неё —
    // нижняя кнопка «Забронировать». После выбора дат модуль сам открывает страницу бронирования
    // (адрес задаётся в кабинете Exely → «Форма поиска»).
    $context = $hotel?->exelyContext() ?? config('tenir.exely.context');
    $exelyLocale = \App\Support\Locales::all()[app()->getLocale()]['exely'] ?? 'ru';
@endphp
<div data-search-bar class="tenir-search sticky top-[84px] z-[36] hidden border-b border-mist bg-sand lg:block">
    <div class="container-site flex min-h-[68px] items-center justify-between gap-8 py-2.5">
        <p class="hidden shrink-0 flex-col gap-0.5 xl:flex">
            <span class="text-[11px] font-medium uppercase tracking-[0.28em] text-forest">{{ __('site.booking.title') }}</span>
            <span class="text-[13px] text-stone">{{ __('site.booking.best_rate') }}</span>
        </p>
        <div id="tl-search-form" class="tl-container min-h-[48px] flex-1"></div>
    </div>
</div>
<noscript><p class="bg-sand py-3 text-center"><a href="{{ route('booking') }}" class="link-line">{{ __('site.booking.search') }}</a></p></noscript>
<script>
    (function (w) {
        var q = [
            ['setContext', @json($context), @json($exelyLocale)],
            ['embed', 'search-form', { container: 'tl-search-form' }]
        ];
        var t = w.travelline = (w.travelline || {}), ti = t.integration = (t.integration || {});
        ti.__cq = ti.__cq ? ti.__cq.concat(q) : q;
        if (!ti.__loader) {
            ti.__loader = true;
            var d = w.document, s = d.createElement('script');
            s.async = true;
            s.src = @json(config('tenir.exely.loader'));
            (d.getElementsByTagName('head')[0] || d.getElementsByTagName('body')[0]).appendChild(s);
        }
    })(window);
</script>

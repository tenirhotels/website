@props(['hotel' => null, 'id' => 'bb'])
@php
    // Плашка бронирования на первом экране — виджет Exely (TravelLine) «search-form», как в V1:
    // календарь с ценами на каждую ночь, наличие номеров, гости. Виджет стоит в собственной панели
    // сайта (песочный фон, подпись слева), нижним краем заходит на фото. После выбора дат модуль
    // сам открывает страницу бронирования (адрес задаётся в кабинете Exely → «Форма поиска»).
    $context = $hotel?->exelyContext() ?? config('tenir.exely.context');
    $exelyLocale = \App\Support\Locales::all()[app()->getLocale()]['exely'] ?? 'ru';
@endphp
<div class="tenir-search bg-sand shadow-[0_20px_50px_rgba(22,33,27,0.22)]">
    <div class="flex flex-col gap-4 px-5 py-5 lg:flex-row lg:items-center lg:gap-10 lg:px-8 lg:py-6">
        <div class="shrink-0 lg:w-52">
            <p class="text-xs font-medium uppercase tracking-[0.3em] text-forest">{{ __('site.booking.title') }}</p>
            <p class="mt-1.5 text-[13px] leading-snug text-stone">{{ __('site.booking.best_rate') }}</p>
        </div>
        <div id="tl-search-form" class="tl-container min-h-[52px] min-w-0 flex-1">
            <noscript><a href="{{ route('booking') }}" class="btn-primary">{{ __('site.booking.search') }}</a></noscript>
        </div>
    </div>
</div>
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

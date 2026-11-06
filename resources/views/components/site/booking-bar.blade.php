{{-- Плашка бронирования под шапкой (страницы без большой формы на первом экране). Виджет Exely — см. exely-search. --}}
<div data-search-bar class="sticky top-[84px] z-[36] hidden border-b border-mist bg-sand lg:block">
    <div class="container-site flex min-h-[68px] items-center justify-between gap-8 py-2.5">
        <p class="hidden shrink-0 flex-col gap-0.5 xl:flex">
            <span class="text-[11px] font-medium uppercase tracking-[0.28em] text-forest">{{ __('site.booking.title') }}</span>
            <span class="text-[13px] text-stone">{{ __('site.booking.best_rate') }}</span>
        </p>
        <div id="tl-search-form" data-state="strip" class="tl-container min-h-[48px] flex-1"></div>
    </div>
</div>
<noscript><p class="bg-sand py-3 text-center"><a href="{{ route('booking') }}" class="link-line">{{ __('site.booking.search') }}</a></p></noscript>

{{-- Большая форма бронирования на первом экране (как у Airbnb / Marriott).
     При прокрутке тот же виджет переезжает в шапку (рядом с логотипом) — см. resources/js/app.js. --}}
<div data-search-slot="hero" class="mt-2 hidden min-h-[116px] lg:block">
    <div id="tl-search-form" data-state="hero" class="tl-container"></div>
    <noscript><a href="{{ route('booking') }}" class="btn-primary">{{ __('site.booking.search') }}</a></noscript>
</div>

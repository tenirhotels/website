@props(['hotel'])
@php
    // Подразделы отеля, как вкладки на странице отеля Marriott / Aman.
    $items = [
        'destinations.show' => __('site.hotel.overview'),
        'destinations.rooms' => __('site.hotel.rooms'),
        'destinations.dining' => __('site.hotel.dining'),
        'destinations.experiences' => __('site.hotel.experiences'),
        'destinations.offers' => __('site.hotel.offers'),
        'destinations.gallery' => __('site.hotel.gallery'),
        'destinations.getting-here' => __('site.hotel.getting_here'),
    ];
    $current = request()->route()->getName();
    if ($current === 'destinations.rooms.show') {
        $current = 'destinations.rooms';
    }
@endphp
<nav aria-label="{{ $hotel->t('name') }}" class="sticky top-[72px] z-30 border-b border-mist bg-snow/95 backdrop-blur lg:top-[84px]">
    <div class="container-site flex items-center justify-between gap-6">
        <ul class="-mb-px flex gap-7 overflow-x-auto whitespace-nowrap text-[13px] uppercase tracking-[0.14em]">
            @foreach ($items as $route => $label)
                <li>
                    <a href="{{ route($route, ['hotel' => $hotel->slug]) }}"
                       @class(['inline-block border-b-2 py-4', 'border-ember text-ink' => $current === $route, 'border-transparent text-stone hover:text-ink' => $current !== $route])
                       @if ($current === $route) aria-current="page" @endif>{{ $label }}</a>
                </li>
            @endforeach
        </ul>
        @if ($hotel->isOpen())
            <a href="{{ route('booking', ['hotel' => $hotel->slug]) }}" data-open-booking class="btn-primary hidden shrink-0 lg:inline-flex">{{ __('site.nav.book') }}</a>
        @endif
    </div>
</nav>

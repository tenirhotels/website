{{-- Фильтр по отелю — только в режиме сети. $route: experiences.index | offers.index --}}
@if ($hotels->count() > 1)
    <nav aria-label="{{ __('site.booking.hotel') }}" class="flex flex-wrap gap-3">
        <a href="{{ route($route, request()->except('hotel')) }}"
           @class(['min-h-11 border px-5 py-2.5 text-[13px] uppercase tracking-[0.14em]', 'border-forest bg-forest text-white' => ! $activeHotel, 'border-mist hover:border-forest' => $activeHotel])>{{ __('site.common.whole_network') }}</a>
        @foreach ($hotels as $h)
            <a href="{{ route($route, [...request()->except('hotel'), 'hotel' => $h->slug]) }}"
               @class(['min-h-11 border px-5 py-2.5 text-[13px] uppercase tracking-[0.14em]', 'border-forest bg-forest text-white' => $activeHotel?->is($h), 'border-mist hover:border-forest' => ! $activeHotel?->is($h)])>{{ $h->t('name') }}</a>
        @endforeach
    </nav>
@endif

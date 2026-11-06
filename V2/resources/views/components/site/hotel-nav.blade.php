@props(['hotel'])
@php
    use App\Support\Property;
    // Подменю отеля (вкладки как у Marriott / Aman) — только в режиме сети.
    // В режиме одного отеля эти разделы и так в главном меню.
    $sections = [
        'overview' => __('site.hotel.overview'),
        'rooms' => __('site.hotel.rooms'),
        'dining' => __('site.hotel.dining'),
        'experiences' => __('site.hotel.experiences'),
        'offers' => __('site.hotel.offers'),
        'gallery' => __('site.hotel.gallery'),
        'getting-here' => __('site.hotel.getting_here'),
    ];
    $routeName = request()->route()?->getName();
    $current = collect(array_keys($sections))->first(fn ($s) => Property::routeName($s, Property::MULTI) === $routeName)
        ?? ($routeName === 'destinations.rooms.show' ? 'rooms' : null);
@endphp
@unless (Property::isSingleHotel($hotel))
    <nav aria-label="{{ $hotel->t('name') }}" class="sticky top-[72px] z-30 border-b border-mist bg-snow/95 backdrop-blur lg:top-[84px]">
        <div class="container-site flex items-center justify-between gap-6">
            <ul class="-mb-px flex gap-7 overflow-x-auto whitespace-nowrap text-[13px] uppercase tracking-[0.14em]">
                @foreach ($sections as $section => $label)
                    <li>
                        <a href="{{ $hotel->sectionUrl($section) }}"
                           @class(['inline-block border-b-2 py-4', 'border-ember text-ink' => $current === $section, 'border-transparent text-stone hover:text-ink' => $current !== $section])
                           @if ($current === $section) aria-current="page" @endif>{{ $label }}</a>
                    </li>
                @endforeach
            </ul>
            @if ($hotel->isOpen())
                <a href="{{ route('booking', ['hotel' => $hotel->slug]) }}" class="btn-primary hidden shrink-0 lg:inline-flex">{{ __('site.nav.book') }}</a>
            @endif
        </div>
    </nav>
@endunless

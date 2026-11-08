@props(['alternates' => []])
@php
    use App\Support\Locales;
    $current = app()->getLocale();
    $brandPages = $navPages->where('placement', 'main');
    $openHotels = $navHotels->where('status', \App\Models\Hotel::STATUS_OPEN);
    $single = \App\Support\Property::isSingle();
    $hotel = \App\Support\Property::primary();

    // Главное меню. Один отель — разделы отеля; сеть — разделы бренда.
    $menu = $single && $hotel
        ? [
            [__('site.hotel.rooms'), $hotel->sectionUrl('rooms')],
            [__('site.hotel.dining'), $hotel->sectionUrl('dining')],
            [__('site.nav.experiences'), route('experiences.index')],
            [__('site.nav.offers'), route('offers.index')],
            [__('site.nav.events'), route('events.index')],
        ]
        : [
            [__('site.nav.destinations'), route('destinations.index')],
            [__('site.nav.experiences'), route('experiences.index')],
            [__('site.nav.offers'), route('offers.index')],
            [__('site.nav.events'), route('events.index')],
        ];
    foreach ($brandPages as $page) {
        $menu[] = [$page->t('title'), $page->url()];
    }
    $mobileExtra = array_filter([
        $single && $hotel ? [__('site.hotel.gallery'), $hotel->sectionUrl('gallery')] : null,
        $single && $hotel ? [__('site.hotel.getting_here'), $hotel->sectionUrl('getting-here')] : null,
        $single && $hotel ? [__('map.open_map'), route('map')] : null,
        \App\Support\Gifts::offers() ? [__('gifts.open'), route('gifts')] : null,
        [__('site.nav.stories'), route('stories.index')],
        [__('site.nav.contact'), route('contact')],
    ]);
@endphp
<header data-header class="sticky top-0 z-40 bg-forest text-snow">
    <div class="container-site flex h-[72px] items-center justify-between gap-6 lg:h-[84px]">
        <a href="{{ route('home') }}" class="shrink-0" aria-label="{{ $single && $hotel ? $hotel->t('name') : 'Tenir Hotels' }}">
            <img src="/images/logo-light.svg" alt="Tenir" width="150" height="36" class="h-8 w-auto lg:h-9">
        </a>

        <nav aria-label="{{ __('site.nav.menu') }}" class="hidden items-center gap-5 text-[13px] xl:gap-8 uppercase tracking-[0.16em] xl:flex">
            @foreach ($menu as [$label, $href])
                <a href="{{ $href }}" class="whitespace-nowrap hover:text-ember-light">{{ $label }}</a>
            @endforeach
        </nav>

        <div class="flex items-center gap-3 sm:gap-4">
            <details class="relative">
                <summary class="flex min-h-10 cursor-pointer list-none items-center gap-1.5 rounded-full border border-ember-light px-4 text-[14px] text-ember-light" aria-label="{{ __('site.nav.language') }}">
                    {{ Locales::all()[$current]['label'] }}
                    <svg aria-hidden="true" width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor"><path d="M3 4.5 6 7.5l3-3"/></svg>
                </summary>
                <ul class="absolute right-0 z-50 mt-3 flex min-w-40 flex-col gap-1 rounded-3xl border border-white/15 bg-forest p-2 shadow-xl">
                    @foreach (Locales::all() as $code => $meta)
                        <li>
                            <a href="{{ $alternates[$code] ?? route('home', ['locale' => $code]) }}" hreflang="{{ $meta['hreflang'] }}" lang="{{ $code }}"
                               @class(['flex min-h-11 items-center rounded-full px-4 text-[15px]', 'bg-white/10 text-ember-light' => $code === $current, 'hover:bg-white/10' => $code !== $current])
                               @if ($code === $current) aria-current="true" @endif>{{ $meta['name'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </details>
            <a href="{{ route('booking') }}" class="btn-primary hidden sm:inline-flex">{{ __('site.nav.book') }}</a>
            <button type="button" class="-mr-2 flex size-11 items-center justify-center xl:hidden" data-menu-toggle aria-controls="mobile-menu" aria-expanded="false" aria-label="{{ __('site.nav.menu') }}">
                <svg class="burger" aria-hidden="true" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path class="b1" d="M3 7h18"/><path class="b2" d="M3 12h18"/><path class="b3" d="M3 17h18"/></svg>
            </button>
        </div>
    </div>

    {{-- Мобильное меню: основные разделы, дополнительные и кнопка бронирования (язык — в шапке) --}}
    <div id="mobile-menu" aria-hidden="true" inert class="fixed inset-x-0 bottom-0 top-[72px] z-40 flex flex-col overflow-y-auto overflow-x-hidden overscroll-contain isolate bg-forest px-6 pb-8 pt-4 xl:hidden">
        <div class="pattern-scene -z-10 bottom-4 right-0 h-[420px] w-[320px] bg-ember-light opacity-[0.10]" aria-hidden="true"></div>
        <nav aria-label="{{ __('site.nav.menu') }}" class="flex flex-col font-display text-[24px] leading-tight">
            @unless ($single)
                @foreach ($openHotels as $h)
                    <a href="{{ $h->url() }}" class="border-b border-white/10 py-3.5">{{ $h->t('name') }}</a>
                @endforeach
            @endunless
            @foreach ($menu as [$label, $href])
                <a href="{{ $href }}" class="border-b border-white/10 py-3.5">{{ $label }}</a>
            @endforeach
        </nav>

        @if ($mobileExtra)
            <nav aria-label="{{ __('site.nav.more') }}" class="mt-6 flex flex-col gap-1 text-[13px] uppercase tracking-[0.16em] text-snow/80">
                @foreach ($mobileExtra as [$label, $href])
                    <a href="{{ $href }}" class="py-2.5">{{ $label }}</a>
                @endforeach
            </nav>
        @endif

        <a href="{{ route('booking') }}" class="btn-primary mt-auto w-full min-h-[52px]" style="margin-top: 2rem">{{ __('site.nav.book') }}</a>
    </div>
</header>

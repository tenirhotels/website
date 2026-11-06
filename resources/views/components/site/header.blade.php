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
        ]
        : [
            [__('site.nav.destinations'), route('destinations.index')],
            [__('site.nav.experiences'), route('experiences.index')],
            [__('site.nav.offers'), route('offers.index')],
        ];
    foreach ($brandPages as $page) {
        $menu[] = [$page->t('title'), $page->url()];
    }
    $mobileExtra = array_filter([
        $single && $hotel ? [__('site.hotel.gallery'), $hotel->sectionUrl('gallery')] : null,
        $single && $hotel ? [__('site.hotel.getting_here'), $hotel->sectionUrl('getting-here')] : null,
        [__('site.nav.stories'), route('stories.index')],
        [__('site.nav.contact'), route('contact')],
    ]);
@endphp
<header data-header class="sticky top-0 z-40 bg-forest text-snow">
    <div class="container-site flex h-[72px] items-center justify-between gap-6 lg:h-[84px]">
        <button type="button" class="-ml-2 flex size-11 items-center justify-center lg:hidden" data-menu-toggle aria-controls="mobile-menu" aria-expanded="false" aria-label="{{ __('site.nav.menu') }}">
            <svg data-icon-open aria-hidden="true" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 7h18M3 12h18M3 17h18"/></svg>
            <svg data-icon-close hidden aria-hidden="true" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M5 5l14 14M19 5 5 19"/></svg>
        </button>

        <a href="{{ route('home') }}" class="shrink-0" aria-label="{{ $single && $hotel ? $hotel->t('name') : 'Tenir Hotels' }}">
            <img src="/images/logo-light.svg" alt="Tenir" width="150" height="36" class="h-8 w-auto lg:h-9">
        </a>

        <nav aria-label="{{ __('site.nav.menu') }}" class="hidden items-center gap-8 text-[13px] uppercase tracking-[0.16em] lg:flex">
            @foreach ($menu as [$label, $href])
                <a href="{{ $href }}" class="hover:text-ember-light">{{ $label }}</a>
            @endforeach
        </nav>

        <div class="flex items-center gap-4">
            <details class="relative hidden sm:block">
                <summary class="flex min-h-11 cursor-pointer list-none items-center gap-1 text-[13px] tracking-[0.1em]" aria-label="{{ __('site.nav.language') }}">
                    {{ Locales::all()[$current]['label'] }}
                    <svg aria-hidden="true" width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor"><path d="M3 4.5 6 7.5l3-3"/></svg>
                </summary>
                <ul class="absolute right-0 mt-2 min-w-36 bg-white py-2 text-ink shadow-lg">
                    @foreach (Locales::all() as $code => $meta)
                        <li>
                            <a href="{{ $alternates[$code] ?? route('home', ['locale' => $code]) }}" hreflang="{{ $meta['hreflang'] }}" lang="{{ $code }}"
                               @class(['block px-4 py-2 text-sm hover:bg-snow', 'text-ember' => $code === $current])
                               @if ($code === $current) aria-current="true" @endif>{{ $meta['name'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </details>
            <a href="{{ route('booking') }}" class="btn-primary hidden sm:inline-flex">{{ __('site.nav.book') }}</a>
        </div>
    </div>

    {{-- Мобильное меню: основные разделы, дополнительные, язык, связь и кнопка бронирования --}}
    <div id="mobile-menu" hidden class="fixed inset-x-0 bottom-0 top-[72px] z-40 flex flex-col overflow-y-auto bg-forest px-6 pb-8 pt-4 lg:hidden">
        <nav aria-label="{{ __('site.nav.menu') }}" class="flex flex-col font-display text-[28px] leading-tight">
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

        <div class="mt-6 flex flex-wrap gap-2 border-t border-white/20 pt-5">
            @foreach (Locales::all() as $code => $meta)
                <a href="{{ $alternates[$code] ?? route('home', ['locale' => $code]) }}" lang="{{ $code }}"
                   @class(['min-h-11 border px-4 py-2.5 text-sm', 'border-ember-light text-ember-light' => $code === $current, 'border-white/30' => $code !== $current])>{{ $meta['label'] }}</a>
            @endforeach
        </div>

        <a href="{{ route('booking') }}" class="btn-primary mt-auto w-full min-h-[52px]" style="margin-top: 2rem">{{ __('site.nav.book') }}</a>
    </div>
</header>

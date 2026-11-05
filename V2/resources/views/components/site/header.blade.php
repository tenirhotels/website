@props(['alternates' => []])
@php
    use App\Support\Locales;
    $current = app()->getLocale();
    $brandPages = $navPages->where('placement', 'main');
    $openHotels = $navHotels->where('status', \App\Models\Hotel::STATUS_OPEN);
@endphp
<header data-header class="sticky top-0 z-40 bg-forest text-snow">
    <div class="container-site flex h-[72px] items-center justify-between gap-6 lg:h-[84px]">
        <button type="button" class="-ml-2 flex size-11 items-center justify-center lg:hidden" data-menu-toggle aria-controls="mobile-menu" aria-expanded="false" aria-label="{{ __('site.nav.menu') }}">
            <svg aria-hidden="true" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 7h18M3 12h18M3 17h18"/></svg>
        </button>

        <a href="{{ route('home') }}" class="shrink-0" aria-label="Tenir Hotels">
            <img src="/images/logo-light.svg" alt="Tenir" width="150" height="36" class="h-8 w-auto lg:h-9">
        </a>

        <nav aria-label="{{ __('site.nav.menu') }}" class="hidden items-center gap-8 text-[13px] uppercase tracking-[0.16em] lg:flex">
            {{-- Пока отель один — ссылка ведёт сразу на него, потом на витрину направлений --}}
            @if ($openHotels->count() === 1)
                <a href="{{ $openHotels->first()->url() }}" class="hover:text-ember-light">{{ $openHotels->first()->t('name') }}</a>
            @else
                <a href="{{ route('destinations.index') }}" class="hover:text-ember-light">{{ __('site.nav.destinations') }}</a>
            @endif
            <a href="{{ route('experiences.index') }}" class="hover:text-ember-light">{{ __('site.nav.experiences') }}</a>
            <a href="{{ route('offers.index') }}" class="hover:text-ember-light">{{ __('site.nav.offers') }}</a>
            @foreach ($brandPages as $page)
                <a href="{{ $page->url() }}" class="hover:text-ember-light">{{ $page->t('title') }}</a>
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
            <a href="{{ route('booking') }}" data-open-booking class="btn-primary hidden sm:inline-flex">{{ __('site.nav.book') }}</a>
        </div>
    </div>

    {{-- Мобильное меню --}}
    <div id="mobile-menu" hidden class="fixed inset-x-0 bottom-0 top-[72px] z-40 overflow-y-auto bg-forest px-6 pb-32 pt-6 lg:hidden">
        <nav aria-label="{{ __('site.nav.menu') }}" class="flex flex-col gap-1 font-display text-3xl">
            @foreach ($openHotels as $h)
                <a href="{{ $h->url() }}" class="py-2">{{ $h->t('name') }}</a>
            @endforeach
            <a href="{{ route('destinations.index') }}" class="py-2">{{ __('site.nav.destinations') }}</a>
            <a href="{{ route('experiences.index') }}" class="py-2">{{ __('site.nav.experiences') }}</a>
            <a href="{{ route('offers.index') }}" class="py-2">{{ __('site.nav.offers') }}</a>
            @foreach ($brandPages as $page)
                <a href="{{ $page->url() }}" class="py-2">{{ $page->t('title') }}</a>
            @endforeach
            <a href="{{ route('stories.index') }}" class="py-2">{{ __('site.nav.stories') }}</a>
            <a href="{{ route('contact') }}" class="py-2">{{ __('site.nav.contact') }}</a>
        </nav>
        <div class="mt-8 flex flex-wrap gap-3 border-t border-white/20 pt-6">
            @foreach (Locales::all() as $code => $meta)
                <a href="{{ $alternates[$code] ?? route('home', ['locale' => $code]) }}" lang="{{ $code }}"
                   @class(['min-h-11 border px-4 py-2.5 text-sm', 'border-ember-light text-ember-light' => $code === $current, 'border-white/30' => $code !== $current])>{{ $meta['label'] }}</a>
            @endforeach
        </div>
    </div>
</header>

{{-- Страница локации: о месте, погода, камеры, лыжные трассы, карта, как добраться. --}}
@php
    use App\Support\Locales;
    use App\Support\Property;

    $name = $location->t('name');
    $facts = $location->placeFacts();
    $cameras = $location->placeCameras();
    $trails = $location->placeTrails();
    $points = $location->weatherPoints();
    $map = $location->resortMap();
    $showMap = $map && Property::mapHotel();
    $heroImage = $location->place_image ?: $hotel?->seasonHero();
    $levelDot = ['green' => 'bg-[#3E9B5B]', 'blue' => 'bg-[#2F6FB5]', 'red' => 'bg-[#C8402F]', 'black' => 'bg-[#222]', 'other' => 'bg-stone'];
    $statusCls = ['open' => 'bg-[#E3F1E7] text-[#23643A]', 'partial' => 'bg-[#FBF0D9] text-[#8A5A0B]', 'closed' => 'bg-[#F6E1DE] text-[#9A2F22]'];
    $i18n = [
        'wx' => __('site.place.wx'), 'today' => __('site.place.today'), 'feels' => __('site.place.feels'), 'wind' => __('site.place.wind'),
        'gust' => __('site.place.gust'), 'ms' => __('site.place.ms'), 'snow' => __('site.place.snow'), 'cm' => __('site.place.cm'),
        'unavailable' => __('site.place.unavailable'), 'note' => __('site.place.weather_note', ['alt' => ':alt', 'time' => ':time']),
    ];
    $trailsNote = Locales::pick((array) data_get($location->place, 'trails_note', []));
@endphp
<x-layouts.site :seo="$seo" :hotel="$hotel">
    @if ($points || $cameras)
        <x-slot:head>@vite('resources/js/place.js')</x-slot:head>
    @endif

    <x-site.page-hero :image="$heroImage" :eyebrow="$location->t('region')" :title="$name" :text="$location->t('lead')" />

    <div data-place data-locale="{{ app()->getLocale() }}" data-i18n='@json($i18n)'>

        {{-- О месте --}}
        @if ($location->t('about') || $facts)
            <section class="container-site flex flex-col gap-10 py-14 md:py-20 lg:flex-row lg:gap-20">
                <div class="flex-[1.5]">
                    <p class="eyebrow">{{ __('site.place.about') }}</p>
                    <h2 class="h-section mt-3">{{ $name }}</h2>
                    @if ($location->t('about'))<div class="prose-tenir mt-6">{!! $location->t('about') !!}</div>@endif
                </div>
                @if ($facts)
                    <aside class="flex-1 lg:max-w-md">
                        <dl class="flex flex-col divide-y divide-forest/10 rounded-3xl bg-white p-6 md:p-8">
                            @foreach ($facts as [$label, $value])
                                <div class="flex items-baseline justify-between gap-6 py-3 first:pt-0 last:pb-0">
                                    <dt class="text-sm text-stone">{{ $label }}</dt>
                                    <dd class="text-right font-medium text-forest">{{ $value }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </aside>
                @endif
            </section>
        @endif

        {{-- Погода --}}
        @if ($points)
            <section class="band band-sand bg-sand py-14 md:py-20">
                <div class="container-site flex flex-col gap-8">
                    <h2 class="h-section">{{ __('site.place.weather') }}</h2>
                    <div class="flex flex-col gap-5">
                        @foreach ($points as $pt)
                            <div class="rounded-3xl bg-white p-6 md:p-8">
                                <p class="mb-5 text-[13px] uppercase tracking-[0.14em] text-stone">
                                    {{ $pt['name'] ? $pt['name'].' · ' : '' }}<span data-weather-stamp>{{ __('site.place.weather_note', ['alt' => number_format($pt['altitude_m'], 0, ',', "\u{00A0}"), 'time' => '…']) }}</span>
                                </p>
                                <div data-alt="{{ number_format($pt['altitude_m'], 0, ',', "\u{00A0}") }}" data-weather="{{ route('weather', ['location' => $location->slug, 'alt' => $pt['altitude_m']]) }}" class="min-h-[120px]">
                                    <p class="text-stone">{{ __('site.place.loading') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <p class="text-[13px] text-stone">{{ __('site.place.weather_model') }}</p>
                </div>
            </section>
        @endif

        {{-- Камеры --}}
        @if ($cameras)
            <section class="container-site flex flex-col gap-8 py-14 md:py-20">
                <h2 class="h-section">{{ __('site.place.cameras') }}</h2>
                <div class="grid gap-5 md:grid-cols-2">
                    @foreach ($cameras as $cam)
                        <figure data-cam data-refresh="{{ $cam['refresh'] }}" class="flex flex-col overflow-hidden rounded-3xl bg-white">
                            <div data-cam-box class="relative aspect-video bg-forest-deep">
                                @if ($cam['image'])
                                    <img data-src="{{ $cam['image'] }}" alt="{{ $cam['title'] }}" loading="lazy" class="absolute inset-0 h-full w-full object-cover">
                                    <p data-cam-error class="absolute inset-0 hidden items-center justify-center bg-forest-deep p-6 text-center text-snow/80 [&:not(.hidden)]:flex">{{ __('site.place.cam_error') }}</p>
                                @else
                                    <div class="absolute inset-0 flex items-center justify-center bg-forest-deep p-6 text-center text-snow/80">{{ $cam['title'] }}</div>
                                @endif
                                @if ($cam['embed'])
                                    <button type="button" data-cam-embed="{{ $cam['embed'] }}" class="btn-primary absolute bottom-4 left-4">{{ __('site.place.cam_online') }}</button>
                                @endif
                            </div>
                            <figcaption class="flex flex-wrap items-center justify-between gap-x-6 gap-y-2 p-5">
                                <span class="font-display text-xl text-forest">{{ $cam['title'] }}</span>
                                <span class="flex flex-wrap items-center gap-x-5 gap-y-1 text-[13px] text-stone">
                                    @if ($cam['image'])<span>{{ __('site.place.cam_updated') }} <span data-cam-stamp>…</span></span>@endif
                                    @if ($cam['page'])<a href="{{ $cam['page'] }}" target="_blank" rel="noopener nofollow" class="link-line">{{ __('site.place.cam_site') }}</a>@endif
                                </span>
                            </figcaption>
                        </figure>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Лыжные трассы --}}
        @if ($trails)
            <section class="band band-sand bg-sand py-14 md:py-20">
                <div class="container-site flex flex-col gap-8">
                    <h2 class="h-section">{{ __('site.place.trails') }}</h2>
                    <div class="grid gap-5 md:grid-cols-2">
                        @foreach ($trails as $level => $items)
                            <div class="flex flex-col gap-1 rounded-3xl bg-white p-6 md:p-8">
                                <p class="mb-3 flex items-center gap-3 font-display text-xl text-forest">
                                    <span class="size-3.5 rounded-full {{ $levelDot[$level] ?? $levelDot['other'] }}"></span>{{ __('site.place.'.$level) }}
                                </p>
                                <ul class="flex flex-col divide-y divide-forest/10">
                                    @foreach ($items as $t)
                                        <li class="flex flex-wrap items-center justify-between gap-x-4 gap-y-1 py-3">
                                            <span class="flex flex-col">
                                                <span class="font-medium text-forest">{{ $t['name'] }}</span>
                                                @if ($t['note'])<span class="text-[13px] text-stone">{{ $t['note'] }}</span>@endif
                                            </span>
                                            <span class="flex items-center gap-3 text-[13px]">
                                                @if ($t['length_m'])<span class="text-stone">{{ number_format($t['length_m'], 0, ',', "\u{00A0}") }} {{ __('site.place.m') }}</span>@endif
                                                <span class="rounded-full px-3 py-1 {{ $statusCls[$t['status']] ?? $statusCls['open'] }}">{{ __('site.place.'.$t['status']) }}</span>
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                    <p class="text-[13px] text-stone">{{ $trailsNote ?: __('site.place.trails_note') }}</p>
                </div>
            </section>
        @endif

        {{-- Карта, как добраться, перед приездом --}}
        <section class="container-site grid gap-5 py-14 md:py-20 lg:grid-cols-3">
            @if ($showMap)
                <a href="{{ route('map') }}" class="group relative flex min-h-[280px] flex-col justify-end gap-2 overflow-hidden rounded-3xl bg-forest-deep p-6 text-snow md:p-8 lg:col-span-2">
                    <img src="{{ $map->image(\App\Support\ResortMap::defaultSeason(), true) }}" alt="" loading="lazy" class="absolute inset-0 h-full w-full object-cover opacity-70 transition-transform duration-700 group-hover:scale-105">
                    <span class="absolute inset-0 bg-[linear-gradient(to_top,rgba(22,33,27,.85),rgba(22,33,27,0)_65%)]"></span>
                    <span class="relative font-display text-2xl md:text-3xl">{{ __('site.place.map') }}</span>
                    <span class="relative max-w-xl text-[15px] text-snow/90">{{ __('site.place.map_text') }}</span>
                    <span class="link-line relative mt-1 self-start text-snow">{{ __('site.place.map_open') }}</span>
                </a>
            @endif
            @if ($hotel)
                <div class="flex flex-col gap-5 @if (! $showMap) lg:col-span-3 lg:grid lg:grid-cols-3 @endif">
                    <a href="{{ $hotel->sectionUrl('getting-here') }}" class="group flex flex-1 flex-col gap-2 rounded-3xl border border-mist bg-white p-6 transition hover:border-ember md:p-8">
                        <span class="font-display text-xl md:text-2xl">{{ __('site.place.getting') }}</span>
                        <span class="text-base leading-relaxed text-slate">{{ __('site.place.getting_text') }}</span>
                    </a>
                    <a href="{{ $hotel->sectionUrl('before-you-arrive') }}" class="group flex flex-1 flex-col gap-2 rounded-3xl border border-mist bg-white p-6 transition hover:border-ember md:p-8">
                        <span class="font-display text-xl md:text-2xl">{{ __('site.place.arrive') }}</span>
                        <span class="text-base leading-relaxed text-slate">{{ __('site.place.arrive_text') }}</span>
                    </a>
                </div>
            @endif
        </section>

        {{-- Где остановиться --}}
        @if ($hotels->isNotEmpty())
            <section class="band band-forest bg-forest py-14 text-snow md:py-20">
                <div class="container-site flex flex-col gap-6">
                    <h2 class="h-section text-snow">{{ __('site.place.stay') }}</h2>
                    <div class="flex flex-wrap gap-4">
                        @foreach ($hotels as $h)
                            <a href="{{ $h->url() }}" class="rounded-full border border-snow/40 px-6 py-3 transition hover:border-snow">{{ $h->t('name') }}</a>
                        @endforeach
                        <a href="{{ route('booking') }}" class="btn-primary">{{ __('site.place.book') }}</a>
                    </div>
                </div>
            </section>
        @endif
    </div>
</x-layouts.site>

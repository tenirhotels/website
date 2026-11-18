{{-- Главная: «Шымбулак сейчас» — температура и камера из страницы локации. Включается в настройках сайта (home.place_now). --}}
@php
    $loc = \App\Models\Setting::get('home.place_now', false) === true ? \App\Models\Location::withPlacePage() : null;
    $pt = $loc?->weatherPoints()[0] ?? null;
    $cam = collect($loc?->placeCameras() ?? [])->first(fn ($c) => $c['image']);
@endphp
@if ($loc && ($pt || $cam))
    @php
        $i18n = [
            'wx' => __('site.place.wx'), 'today' => __('site.place.today'), 'feels' => __('site.place.feels'), 'wind' => __('site.place.wind'),
            'gust' => __('site.place.gust'), 'ms' => __('site.place.ms'), 'snow' => __('site.place.snow'), 'cm' => __('site.place.cm'),
            'unavailable' => __('site.place.unavailable'), 'note' => __('site.place.weather_note', ['alt' => ':alt', 'time' => ':time']),
        ];
    @endphp
    @vite('resources/js/place.js')
    <section class="band band-sand bg-sand">
        <div data-place data-compact data-locale="{{ app()->getLocale() }}" data-i18n='@json($i18n)' class="container-site flex flex-col gap-8 md:gap-10 py-14 md:py-20">
            <x-site.section-head :eyebrow="$loc->t('region')" :title="__('site.place.now_title', ['name' => $loc->t('name')])" :link="$loc->placeUrl()" :linkLabel="__('site.place.more')" />
            <div class="grid gap-5 {{ $pt && $cam ? 'lg:grid-cols-2' : '' }}">
                @if ($pt)
                    <div class="flex flex-col justify-center gap-4 rounded-3xl bg-white p-6 md:p-8">
                        <p class="text-[13px] uppercase tracking-[0.14em] text-stone">
                            {{ $pt['name'] ? $pt['name'].' · ' : '' }}<span data-weather-stamp>{{ __('site.place.weather_note', ['alt' => number_format($pt['altitude_m'], 0, ',', "\u{00A0}"), 'time' => '…']) }}</span>
                        </p>
                        <div data-alt="{{ number_format($pt['altitude_m'], 0, ',', "\u{00A0}") }}" data-weather="{{ route('weather', ['location' => $loc->slug, 'alt' => $pt['altitude_m']]) }}" class="min-h-[96px]">
                            <p class="text-stone">{{ __('site.place.loading') }}</p>
                        </div>
                    </div>
                @endif
                @if ($cam)
                    <figure data-cam data-refresh="{{ $cam['refresh'] }}" class="relative aspect-video overflow-hidden rounded-3xl bg-forest-deep">
                        <img data-src="{{ $cam['image'] }}" alt="{{ $cam['title'] }}" loading="lazy" class="absolute inset-0 h-full w-full object-cover">
                        <p data-cam-error class="absolute inset-0 hidden items-center justify-center bg-forest-deep p-6 text-center text-snow/80 [&:not(.hidden)]:flex">{{ __('site.place.cam_error') }}</p>
                        <figcaption class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/60 to-transparent p-4 text-sm text-snow">{{ $cam['title'] }} · <span data-cam-stamp>…</span></figcaption>
                    </figure>
                @endif
            </div>
        </div>
    </section>
@endif

@php
    use App\Support\ResortMap;

    $dining = $hotel?->sectionUrl('dining');
    $W = $map->width();
    $H = $map->height();
    $stations = $map->stations();
    $pins = $map->pins();
    $label = fn (array $p) => $p['kind'] === 'hotel' && $hotel ? $hotel->t('name') : $p['label'];
    $restaurants = $pins->where('kind', 'restaurant')->sortBy('n');
    $activities = fn (string $s) => $pins->where('kind', 'activity')->filter(fn ($p) => in_array($s, $p['seasons'] ?? [], true))->sortBy('n');
    $i18n = collect(['locating', 'here', 'here_acc', 'approx', 'far', 'denied', 'unavailable'])->mapWithKeys(fn ($k) => [$k => __('map.'.$k)]);
    $geo = ['points' => $map->geoPoints(), 'width' => $W, 'height' => $H];
    $oneWay = $map->oneWay() ? __('map.one_way_at', ['min' => $map->oneWay(), 'unit' => __('map.min')]) : null;
@endphp
<x-layouts.site :seo="$seo" :hotel="$hotel">
    <x-slot:head>
        <link rel="preload" as="image" href="{{ $map->image($season, true) }}" type="image/webp">
        @vite('resources/js/map.js')
    </x-slot:head>

    <section class="bg-snow pt-8 md:pt-12" data-map-root data-season="{{ $season }}" data-w="{{ $W }}" data-h="{{ $H }}">
        <div class="container-site">
            <div class="flex flex-wrap items-end justify-between gap-x-10 gap-y-5">
                <div class="max-w-2xl">
                    <p class="eyebrow">{{ $hotel?->t('name') ?? __('map.eyebrow') }} · {{ __('map.eyebrow') }}</p>
                    <h1 class="h-display mt-2 text-4xl text-forest md:text-5xl">{{ __('map.title') }}</h1>
                    <p class="lead mt-4">{{ __('map.lead') }}</p>
                </div>
                <div role="group" aria-label="{{ __('map.season_label') }}" class="inline-flex rounded-full border border-mist bg-white p-1 text-[13px] uppercase tracking-[0.14em]">
                    @foreach (['winter', 'summer'] as $s)
                        <button type="button" data-season-btn="{{ $s }}" aria-pressed="{{ $s === $season ? 'true' : 'false' }}"
                                class="min-h-10 rounded-full px-5 text-stone transition-colors aria-pressed:bg-forest aria-pressed:text-white">{{ __('map.'.$s) }}</button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Карта: на телефоне во всю ширину экрана --}}
        <div class="mt-6 md:container-site md:mt-8">
            <div class="map-viewport relative h-[66svh] min-h-[420px] overflow-hidden bg-[#dce9f6] md:h-[72vh] md:max-h-[780px] md:rounded-3xl" data-viewport tabindex="0" aria-label="{{ __('map.title') }}">
                <div class="map-stage" data-stage style="width:{{ $W }}px;height:{{ $H }}px">
                    @foreach (['winter', 'summer'] as $s)
                        <img src="{{ $map->image($s, true) }}" data-full="{{ $map->image($s) }}" data-layer="{{ $s }}" alt="" width="{{ $W }}" height="{{ $H }}"
                             class="map-layer" draggable="false" decoding="async" @if ($s !== $season) data-off @endif>
                    @endforeach

                    {{-- Подписи поверх карты (время в пути и т.п.): задаются в локации --}}
                    @foreach ($map->overlay() as $o)
                        @php $arrow = in_array($o['kind'], ['arrow', 'arrow_time'], true); @endphp
                        <span class="map-time {{ $o['kind'] === 'total' ? 'is-total' : '' }} {{ $o['kind'] === 'name' ? 'is-name' : '' }} {{ $arrow ? 'is-arrow' : '' }}"
                              style="left:{{ $o['x'] }}px;top:{{ $o['y'] }}px{{ $arrow ? ';transform:translate(-50%,-50%) rotate('.$o['rot'].'deg)' : '' }}" aria-hidden="true">{{ $o['text'] }}</span>
                    @endforeach

                    @foreach ($pins as $p)
                        @php
                            $seasons = $p['seasons'] ?? null;
                            $note = null;
                            $lines = [];
                            if (isset($p['cable']) && ($idx = $stations->search(fn ($c) => $c['key'] === $p['cable'])) !== false) {
                                $cur = $stations[$idx];
                                if ($cur['next'] && isset($stations[$idx + 1])) {
                                    $lines[] = '↑ '.__('map.up').($cur['lift'] ? ' ('.$cur['lift'].')' : '').': '.$stations[$idx + 1]['tag'].' · ≈'.$cur['next'].' '.__('map.min');
                                }
                                if ($idx > 0 && $stations[$idx - 1]['next']) {
                                    $lines[] = '↓ '.__('map.down').($stations[$idx - 1]['lift'] ? ' ('.$stations[$idx - 1]['lift'].')' : '').': '.$stations[$idx - 1]['tag'].' · ≈'.$stations[$idx - 1]['next'].' '.__('map.min');
                                }
                            }
                            $note = $lines ? implode("\n", $lines) : null;
                        @endphp
                        <button type="button" id="pin-{{ $p['id'] }}" data-pin="{{ $p['id'] }}" data-kind="{{ $p['kind'] }}"
                                data-name="{{ $label($p) }}" data-kind-label="{{ __('map.kinds.'.$p['kind']) }}"
                                @if ($seasons) data-seasons="{{ implode(' ', $seasons) }}" @endif
                                @if ($p['detail'] ?? false) data-detail @endif
                                @if (! empty($p['venue_url'])) data-href="{{ $p['venue_url'] }}" data-href-label="{{ __('map.dining_link') }}" @endif
                                @if ($note) data-note="{{ $note }}" @elseif (! empty($p['hours'])) data-note="{{ __('site.hotel.hours') }}: {{ $p['hours'] }}" @endif
                                class="map-pin" style="left:{{ $p['x'] }}px;top:{{ $p['y'] }}px" aria-label="{{ $p['n'] ? $p['n'].'. ' : '' }}{{ $label($p) }}">
                            @if ($p['n'])
                                <span class="map-badge">{{ $p['n'] }}</span>
                            @else
                                <span class="map-tag"><i></i>{{ $p['kind'] === 'station' ? $p['tag'] : $label($p) }}</span>
                            @endif
                        </button>
                    @endforeach

                    <span class="map-acc" data-acc hidden aria-hidden="true"></span>
                    <span class="map-me" data-me hidden><i></i><b></b></span>
                </div>

                <div class="map-controls" role="group">
                    <button type="button" data-zoom="in" aria-label="{{ __('map.zoom_in') }}"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg></button>
                    <button type="button" data-zoom="out" aria-label="{{ __('map.zoom_out') }}"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M5 12h14"/></svg></button>
                    <button type="button" data-zoom="fit" aria-label="{{ __('map.fit') }}"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M4 9V4h5M20 9V4h-5M4 15v5h5M20 15v5h-5"/></svg></button>
                    <button type="button" data-locate aria-label="{{ __('map.locate') }}"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="12" cy="12" r="3.2"/><path d="M12 2.5v4M12 17.5v4M2.5 12h4M17.5 12h4"/></svg></button>
                </div>

                <p class="map-toast" data-toast hidden role="status"></p>

                <div class="map-card" data-card hidden>
                    <button type="button" data-card-close class="map-card-close" aria-label="{{ __('map.close') }}">×</button>
                    <p class="eyebrow" data-card-kind></p>
                    <h2 class="h-display mt-1 text-2xl text-forest md:text-3xl" data-card-name></h2>
                    <p class="mt-2 whitespace-pre-line text-base leading-relaxed text-slate" data-card-note hidden></p>
                    <a class="link-line mt-4" data-card-link hidden></a>
                </div>
            </div>
            <p class="container-site mt-3 text-xs text-stone md:px-0">{{ __('map.schematic') }}</p>
            <noscript><p class="container-site py-4 text-stone">{{ __('map.noscript') }}</p></noscript>
        </div>

        {{-- Легенда и списки --}}
        <div class="container-site grid gap-12 py-12 md:py-16 lg:grid-cols-12 lg:gap-16">
            <div class="lg:col-span-5">
                <h2 class="h-section">{{ __('map.cable_title') }}</h2>
                @if ($oneWay)<p class="mt-2 font-display text-2xl text-ember">{{ $oneWay }}</p>@endif
                <ol class="mt-8 space-y-0">
                    @foreach ($stations as $c)
                        <li class="relative flex gap-4 pb-7 last:pb-0">
                            @unless ($loop->last)<span class="absolute left-[7px] top-4 h-full w-px bg-mist" aria-hidden="true"></span>@endunless
                            <span class="relative mt-1.5 size-[15px] shrink-0 rounded-full border-2 border-forest bg-snow"></span>
                            <div>
                                <button type="button" data-focus="st-{{ $c['key'] }}" class="text-left text-lg text-ink hover:text-ember">{{ $c['name'] }}</button>
                                @if ($c['next'])<p class="text-[15px] text-stone">≈{{ $c['next'] }} {{ __('map.min') }} {{ __('map.next') }}</p>@endif
                            </div>
                        </li>
                    @endforeach
                </ol>
                @if ($oneWay)<p class="mt-6 text-[15px] text-stone">{{ __('map.one_way_note') }}</p>@endif
                @if ($hotel)
                    <a href="{{ $hotel->sectionUrl('getting-here') }}" class="link-line mt-8">{{ __('map.getting_here') }}</a>
                @endif
            </div>

            <div class="grid gap-10 sm:grid-cols-2 lg:col-span-7">
                <div>
                    <h2 class="eyebrow mb-4">{{ __('map.g_restaurants') }}</h2>
                    <ul class="space-y-1">
                        @foreach ($restaurants as $p)
                            <li><button type="button" data-focus="{{ $p['id'] }}" class="map-row"><span class="map-badge static">{{ $p['n'] }}</span>{{ $label($p) }}</button></li>
                        @endforeach
                        <li><span class="map-row cursor-default"><img src="/images/map/legend/street.webp" alt="" width="28" height="28" class="size-7">{{ __('map.services.street') }}</span></li>
                    </ul>
                </div>

                @foreach (['winter', 'summer'] as $s)
                    <div data-for-season="{{ $s }}">
                        <h2 class="eyebrow mb-4">{{ __('map.g_activities') }}</h2>
                        <ul class="space-y-1">
                            @foreach ($activities($s) as $p)
                                <li><button type="button" data-focus="{{ $p['id'] }}" class="map-row"><span class="map-badge static is-activity">{{ $p['n'] }}</span>{{ $label($p) }}</button></li>
                            @endforeach
                        </ul>
                        <h2 class="eyebrow mb-4 mt-8">{{ __('map.g_free') }}</h2>
                        <ul class="space-y-1">
                            @foreach (ResortMap::freeActivities($s) as $f)
                                <li><span class="map-row cursor-default"><img src="/images/map/legend/{{ $f['icon'] }}.webp" alt="" width="28" height="28" class="size-7">{{ __('map.services.'.$f['key']) }}</span></li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach

                <div class="sm:col-span-2">
                    <h2 class="eyebrow mb-4">{{ __('map.g_services') }}</h2>
                    @foreach (['winter', 'summer'] as $s)
                        <ul data-for-season="{{ $s }}" class="grid grid-cols-1 gap-x-8 gap-y-1 sm:grid-cols-2">
                            @foreach (ResortMap::services($s) as $svc)
                                @continue($svc['key'] === 'street')
                                <li><span class="map-row cursor-default"><img src="/images/map/legend/{{ $svc['icon'] }}.webp" alt="" height="28" class="h-7 w-auto">{{ __('map.services.'.$svc['key']) }}</span></li>
                            @endforeach
                        </ul>
                    @endforeach
                </div>
            </div>
        </div>

        <script type="application/json" data-map-geo>@json($geo)</script>
        <script type="application/json" data-map-i18n>@json($i18n)</script>
    </section>
</x-layouts.site>

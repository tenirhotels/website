{{-- «Только по канатной дороге»: маршрут с временем и иллюстрированная карта курорта (V1). --}}
@props(['hotel'])
@php
    use App\Support\ResortMap;

    $stations = collect(ResortMap::CABLE);
    $pins = collect(ResortMap::pins())->keyBy('id');
    $season = ResortMap::defaultSeason();
    $rides = $stations->whereNotNull('next')->count();
    $route = $stations->map(fn ($s) => $pins['st-'.$s['key']])->push($pins['hotel']);
    $points = $route->map(fn ($p) => $p['x'].','.$p['y'])->implode(' ');
    $W = ResortMap::WIDTH;
    $H = ResortMap::HEIGHT;
@endphp

<section class="container-site pt-12 md:pt-16">
    <div class="grid overflow-hidden rounded-3xl bg-forest text-white lg:grid-cols-[1.25fr_1fr]">
        <div class="flex flex-col gap-5 p-7 md:p-12">
            <p class="eyebrow-light">{{ __('access.eyebrow') }}</p>
            <h2 class="font-display text-[28px] leading-tight md:text-4xl">{{ __('access.title') }}</h2>
            <p class="text-lg leading-relaxed text-white/80">{{ __('access.text') }}</p>
        </div>
        <dl class="grid grid-cols-3 border-t border-white/15 lg:grid-cols-1 lg:border-l lg:border-t-0">
            @foreach ([
                ['3 200 '.__('access.m'), __('access.stat_altitude')],
                ['≈ '.ResortMap::ONE_WAY_MINUTES.' '.__('map.min'), __('access.stat_time')],
                [$rides, __('access.stat_rides')],
            ] as [$value, $label])
                <div class="flex flex-col justify-center gap-1 border-white/15 px-4 py-5 md:p-8 [&:not(:first-child)]:border-l lg:[&:not(:first-child)]:border-l-0 lg:[&:not(:first-child)]:border-t">
                    <dt class="whitespace-nowrap font-display text-xl leading-none text-ember-light sm:text-2xl md:text-4xl">{{ $value }}</dt>
                    <dd class="text-[13px] leading-snug text-white/70">{{ $label }}</dd>
                </div>
            @endforeach
        </dl>
    </div>
</section>

<section class="bg-white">
    <div class="container-site grid items-center gap-10 py-12 md:py-16 lg:grid-cols-2 lg:gap-20">
        {{-- Профиль высот из V1: Алматы → Медеу → отель --}}
        <div class="relative mx-auto aspect-[426/500] w-full max-w-[480px] overflow-hidden rounded-3xl bg-forest-deep">
            <x-site.media :src="$hotel->gallery[0] ?? $hotel->hero_image" alt="" round="" class="absolute inset-0 opacity-40" />
            <div class="absolute inset-0 bg-gradient-to-t from-forest-deep/80 via-forest/50 to-forest/20"></div>
            <p class="absolute left-4 top-4 rounded-full bg-white/15 px-3 py-1 text-[13px] text-white backdrop-blur-sm">{{ __('access.altitude') }}</p>
            <svg viewBox="0 0 426 500" class="absolute inset-0 h-full w-full" aria-hidden="true">
                <path d="M27.671 446.067L70.9372 422.205C70.9372 422.205 75.2514 417.296 76.9682 413.552C78.6867 409.804 76.9405 406.484 79.5904 403.325C81.713 400.795 84.2491 401.054 86.9326 399.13C99.6256 390.025 95.5211 376.078 103.977 362.943C112.433 349.809 112.466 348.827 120.234 341.966C126.28 336.626 130.273 334.189 137.541 330.69C142.854 328.132 145.853 326.203 151.701 325.446C155.052 325.012 157.067 326.229 160.354 325.446C166.313 324.027 169.007 323.873 172.678 315.744C176.349 307.615 174.776 300.535 174.514 291.095C174.252 281.655 176.699 273.392 176.874 261.989C176.915 259.327 176.387 257.789 176.874 255.171C178.077 248.708 183.966 247.577 187.1 241.798C188.806 238.654 188.688 236.315 190.771 233.407C195.122 227.335 200.251 225.802 207.553 224.229C212.959 223.065 216.281 223.193 221.713 224.229C225.308 224.915 227.094 228.059 230.629 227.114C232.632 226.578 233.832 225.88 235.086 224.229C237.957 220.453 234.3 213.741 232.202 212.429C230.104 211.118 228.737 210.457 227.22 208.496C224.118 204.487 226.536 197.567 226.695 192.501C227.04 181.547 227.673 181.692 235.086 173.621C237.146 171.379 239.282 164.706 242.429 164.706C244.537 164.706 245.856 165.662 247.149 167.328C249.648 170.548 245.128 173.478 245.051 177.554C244.932 183.813 245.837 194.336 249.246 193.287C252.183 192.384 248.705 188.183 249.246 185.159C249.951 181.225 251.219 178.134 254.491 175.194C256.399 173.479 258.589 173.733 260.259 171.785C261.512 170.325 260.816 168.961 261.833 167.328C266 160.631 274.668 163.673 279.401 157.363C283.563 151.816 277.945 137.744 284.384 140.319C289.628 142.417 281.237 161.559 284.384 161.559C286.432 161.559 287.656 161.006 289.628 161.559C293.324 162.596 292.624 168.279 296.446 168.639C299.684 168.943 300.617 165.433 303.788 164.706C306.483 164.087 308.384 164.79 310.081 162.608C311.401 160.91 315.326 156.839 316.899 155.79C318.472 154.741 322.143 152.119 324.765 150.808C326.689 149.846 327.494 146.226 325.552 145.301C324.628 144.861 322.483 145.851 321.619 145.301C318.734 143.466 322.842 139.087 325.552 136.386C327.533 134.412 328.952 134.464 331.321 132.977C337.341 129.199 337.58 123.839 342.859 119.079C350.343 112.33 367.245 112.262 366.458 107.542C365.741 103.241 356.232 110.591 356.232 106.231C356.232 102.822 362.063 102.149 366.458 100.2C372.057 97.7161 374.47 96.8576 380.094 94.4307C384.257 92.6339 386.984 92.2078 390.582 89.4486C395.166 85.9343 395.792 82.0783 399.498 77.6487C407.026 68.6514 421 57.72 421 56.6712" fill="none" stroke="#6B9EC3" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                @foreach ([[27.9, 446.1], [242.4, 164.4], [421.3, 56.9]] as [$cx, $cy])
                    <circle cx="{{ $cx }}" cy="{{ $cy }}" r="6" fill="#EBEBEB"/>
                @endforeach
            </svg>
            @foreach ([
                ['6.6%', '89.2%', false, __('access.level_almaty'), '900'],
                ['56.9%', '32.9%', true, __('access.level_medeu'), '1 700'],
                ['98.9%', '11.4%', true, $hotel->t('name'), '3 200'],
            ] as [$x, $y, $left, $name, $alt])
                <div class="absolute -translate-y-1/2 text-white {{ $left ? '-translate-x-full pr-4 text-right' : 'pl-4' }}" style="left: {{ $x }}; top: {{ $y }}">
                    <p class="text-base font-bold leading-tight md:text-lg">{{ $name }}</p>
                    <p class="text-[15px] leading-tight text-white/80">{{ $alt }} {{ __('access.m') }}</p>
                </div>
            @endforeach
        </div>

        <div class="flex flex-col gap-8">
            <div class="flex flex-col gap-3">
                <h2 class="font-display text-2xl md:text-3xl">{{ __('access.levels_title') }}</h2>
                <p class="text-lg leading-relaxed text-slate">{{ __('access.levels_text') }}</p>
            </div>
            <ol class="flex flex-col divide-y divide-mist border-y border-mist">
                @foreach ([
                    ['car', __('access.leg_car'), __('access.leg_car_meta'), '900 → 1 700'],
                    ['cable', __('access.leg_cable'), __('access.leg_cable_meta', ['min' => ResortMap::ONE_WAY_MINUTES]), '1 700 → 3 200'],
                ] as [$icon, $title, $meta, $alt])
                    <li class="flex items-center gap-4 py-5">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-sand text-ember">{{ site_icon($icon, 'h-6 w-6') }}</span>
                        <div class="flex flex-1 flex-col gap-0.5">
                            <p class="text-lg leading-snug">{{ $title }}</p>
                            <p class="text-[15px] text-stone">{{ $meta }}</p>
                            <p class="mt-1 text-[15px] text-forest"><span class="font-display text-lg">{{ $alt }} {{ __('access.m') }}</span> · {{ __('access.asl') }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>
</section>
<section class="container-site pb-4 pt-12 md:pt-16">
    @php
        $cableNote = trim((string) \App\Models\Setting::get('cable.hours_note', ''));
        $lifts = [
            [__('map.tags.station_medeu').' → '.__('map.tags.station_shymbulak'), \App\Models\Setting::get('cable.hours_medeu') ?: '09:00–18:00'],
            ['Combi-1', \App\Models\Setting::get('cable.hours_combi1') ?: '09:00–17:00'],
            ['Combi-2', \App\Models\Setting::get('cable.hours_combi2') ?: '09:00–16:30'],
        ];
        $endOf = fn (string $h, string $fallback) => preg_match('/(\d{1,2}[:.]\d{2})\s*$/u', $h, $m) ? str_replace('.', ':', $m[1]) : $fallback;
        $medeuClose = $endOf((string) $lifts[0][1], '18:00');
        $combi2Close = $endOf((string) $lifts[2][1], '16:30');
        $medeuLast = \Illuminate\Support\Carbon::createFromFormat('H:i', $medeuClose)->subMinutes(30);
        $dayLast = \Illuminate\Support\Carbon::createFromFormat('H:i', $combi2Close)->subMinutes(30 + 48 + 30);
        $dayLast->setTime($dayLast->hour, intdiv($dayLast->minute, 15) * 15);
        $guestFrom = \App\Models\Setting::get('cable.guest_from') ?: '18:00';
        $guestTo = \App\Models\Setting::get('cable.guest_to') ?: '19:00';
    @endphp
    <h2 class="font-display text-2xl md:text-3xl">{{ __('access.hours_title') }}</h2>
    <p class="mt-3 max-w-2xl text-lg leading-relaxed text-slate">{{ __('access.hours_text') }}</p>
    <ol class="mt-8 grid gap-4 md:grid-cols-3">
        @foreach ([
            ['day', $dayLast->format('H:i'), 'bg-white border border-mist', 'text-ember'],
            ['evening', $medeuLast->format('H:i'), 'bg-forest text-white', 'text-ember-light'],
            ['late', '—', 'bg-white border border-mist', 'text-stone'],
        ] as [$k, $t, $box, $tc])
            <li class="flex flex-col gap-2 rounded-3xl p-6 md:p-8 {{ $box }}">
                <p class="text-[13px] uppercase tracking-[0.18em] {{ $k === 'evening' ? 'text-white/70' : 'text-stone' }}">{{ __('access.when_'.$k) }}</p>
                <p class="font-display text-3xl {{ $tc }}">{{ $k === 'late' ? __('access.when_late_value', ['to' => $guestTo]) : __('access.when_at_medeu', ['time' => $t]) }}</p>
                <p class="text-[15px] leading-relaxed {{ $k === 'evening' ? 'text-white/80' : 'text-slate' }}">{{ __('access.when_'.$k.'_text', ['time' => $t, 'from' => $guestFrom, 'to' => $guestTo, 'medeu_close' => $medeuClose]) }}</p>
            </li>
        @endforeach
    </ol>
    <h3 class="mt-12 font-display text-xl md:text-2xl">{{ __('access.hours_table') }}</h3>
    <div class="mt-5 grid gap-4 md:grid-cols-2">
        <div class="flex flex-col gap-2 rounded-3xl border border-mist p-6 md:p-8">
            <span class="flex h-11 w-11 items-center justify-center rounded-full bg-sand text-ember">{{ site_icon('cable', 'h-6 w-6') }}</span>
            <p class="eyebrow mt-2">{{ __('access.hours_standard') }}</p>
            <ul class="divide-y divide-mist">
                @foreach ($lifts as [$lname, $lhours])
                    <li class="flex items-baseline justify-between gap-4 py-2.5">
                        <span class="text-lg">{{ $lname }}</span>
                        <span class="whitespace-nowrap font-display text-xl">{{ $lhours }}</span>
                    </li>
                @endforeach
            </ul>
            <p class="text-[15px] leading-relaxed text-stone">{{ $cableNote !== '' ? $cableNote.'. ' : '' }}{{ __('access.hours_last') }}</p>
        </div>
        <div class="flex flex-col gap-2 rounded-3xl bg-forest p-6 text-white md:p-8">
            <span class="flex h-11 w-11 items-center justify-center rounded-full bg-white/10 text-ember-light">{{ site_icon('cable', 'h-6 w-6') }}</span>
            <p class="eyebrow-light mt-2">{{ __('access.hours_guest') }}</p>
            <p class="whitespace-nowrap font-display text-3xl text-ember-light">{{ $guestFrom }}–{{ $guestTo }}</p>
            <p class="text-[15px] leading-relaxed text-white/80">{{ __('access.hours_guest_text') }}</p>
        </div>
    </div>
</section>
<section class="container-site py-12 md:py-16">
    <h2 class="font-display text-2xl md:text-3xl">{{ __('access.route_title') }}</h2>
    <ol class="mt-8 grid gap-0 md:grid-cols-5">
        @foreach ($stations as $i => $s)
            <li class="relative flex gap-4 pb-8 md:flex-col md:gap-3 md:pb-0">
                {{-- линия между остановками --}}
                <span class="absolute left-[15px] top-8 h-[calc(100%-2rem)] w-px bg-ember/40 md:left-8 md:top-[15px] md:h-px md:w-[calc(100%-2rem)]" aria-hidden="true"></span>
                <span class="relative z-10 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-ember text-[13px] text-white">{{ $i + 1 }}</span>
                <div class="flex flex-col gap-1">
                    <p class="text-lg leading-snug">{{ __('map.names.station_'.$s['key']) }}</p>
                    @if ($s['next'])
                        <p class="text-[15px] text-stone">↑ ≈ {{ $s['next'] }} {{ __('map.min') }}</p>
                    @endif
                </div>
            </li>
        @endforeach
        <li class="relative flex gap-4 md:flex-col md:gap-3">
            <span class="relative z-10 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-forest text-white">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true"><path d="M3 11l9-7 9 7v9H3z"/><path d="M10 20v-6h4v6"/></svg>
            </span>
            <div class="flex flex-col gap-1">
                <p class="text-lg leading-snug">{{ $hotel->t('name') }}</p>
                <p class="text-[15px] text-stone">{{ __('access.at_hotel') }}</p>
            </div>
        </li>
    </ol>

    {{-- Тот же маршрут на иллюстрированной карте курорта --}}
    <div class="mt-10 md:mt-14">
        <a href="{{ route('map') }}" class="relative block overflow-hidden rounded-3xl bg-[#dce9f6]" aria-label="{{ __('map.open_map') }}">
            <img src="/images/map/{{ $season }}-sm.webp" alt="" width="{{ $W }}" height="{{ $H }}" loading="lazy" decoding="async" class="block h-auto w-full">
            <svg viewBox="0 0 {{ $W }} {{ $H }}" class="absolute inset-0 h-full w-full" aria-hidden="true">
                <polyline points="{{ $points }}" fill="none" stroke="#fff" stroke-width="22" stroke-linecap="round" stroke-linejoin="round" opacity=".9"/>
                <polyline points="{{ $points }}" fill="none" stroke="#B5561F" stroke-width="11" stroke-linecap="round" stroke-linejoin="round"/>
                @foreach ($route as $i => $p)
                    @if ($p['kind'] === 'hotel')
                        <circle cx="{{ $p['x'] }}" cy="{{ $p['y'] }}" r="42" fill="#1F3A2E" stroke="#fff" stroke-width="8"/>
                        <path transform="translate({{ $p['x'] - 20 }} {{ $p['y'] - 21 }}) scale(1.7)" d="M3 11l9-7 9 7v9H3z" fill="none" stroke="#fff" stroke-width="1.8" stroke-linejoin="round"/>
                    @else
                        <circle cx="{{ $p['x'] }}" cy="{{ $p['y'] }}" r="42" fill="#B5561F" stroke="#fff" stroke-width="8"/>
                        <text x="{{ $p['x'] }}" y="{{ $p['y'] + 16 }}" text-anchor="middle" font-size="46" font-weight="700" fill="#fff">{{ $i + 1 }}</text>
                    @endif
                @endforeach
            </svg>
            {{-- подписи (на телефоне названия — в списке шагов выше) --}}
            @foreach ($route as $i => $p)
                @php
                    $name = $p['kind'] === 'hotel' ? $hotel->t('name') : __('map.tags.station_'.$stations[$i]['key']);
                    $side = $p['kind'] === 'hotel' ? 'left' : ($i < 2 ? 'right' : 'left');
                @endphp
                <span class="pointer-events-none absolute hidden -translate-y-1/2 whitespace-nowrap rounded-full bg-white px-3 py-1 text-[13px] text-ink shadow-md md:block {{ $side === 'right' ? 'ml-9' : '-translate-x-full -ml-9' }}"
                      style="left: {{ round($p['x'] / $W * 100, 2) }}%; top: {{ round(($p['y'] - ($p['kind'] === 'hotel' ? 55 : 0)) / $H * 100, 2) }}%">{{ $name }}</span>
            @endforeach
        </a>
        <div class="mt-5 flex flex-wrap items-center justify-between gap-4">
            <p class="max-w-xl text-slate">{{ __('access.map_text') }}</p>
            <a href="{{ route('map') }}" class="btn-dark">{{ __('map.open_map') }}</a>
        </div>
    </div>
</section>



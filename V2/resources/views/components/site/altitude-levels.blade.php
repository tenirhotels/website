@props(['hotel'])
{{-- Профиль высот (как в V1): линия от города до отеля, точки с названиями и высотой; рядом — участки пути. Уровни — из локации. --}}
@php
    $levels = collect($hotel->levels ?? [])->filter(fn ($l) => ($l['altitude_m'] ?? 0) > 0)->sortBy('altitude_m')->values();
    $n = $levels->count();
    $min = (int) $levels->min('altitude_m');
    $max = (int) $levels->max('altitude_m');
    $fmt = fn ($m) => number_format((int) $m, 0, ',', "\u{00A0}");
    $nameOf = fn ($l) => \App\Support\Locales::pick($l['name'] ?? []);
    if ($n > 1 && $max > $min) {
        // Холст 520×320: x — равномерно, y — по высоте (внизу город, вверху отель).
        [$W, $H, $x0, $x1, $yBottom, $yTop] = [520, 320, 62, 458, 205, 95];
        $pts = $levels->map(fn ($l, $i) => [
            'x' => $x0 + $i * ($x1 - $x0) / ($n - 1),
            'y' => $yBottom - ($l['altitude_m'] - $min) / ($max - $min) * ($yBottom - $yTop),
            'name' => $nameOf($l), 'alt' => $l['altitude_m'],
        ])->all();
        $d = 'M'.round($pts[0]['x'], 1).' '.round($pts[0]['y'], 1);
        for ($i = 1; $i < $n; $i++) {
            $mx = ($pts[$i - 1]['x'] + $pts[$i]['x']) / 2;
            $d .= ' C'.round($mx, 1).' '.round($pts[$i - 1]['y'], 1).' '.round($mx, 1).' '.round($pts[$i]['y'], 1).' '.round($pts[$i]['x'], 1).' '.round($pts[$i]['y'], 1);
        }
        $area = $d.' L'.round($pts[$n - 1]['x'], 1).' '.$H.' L'.round($pts[0]['x'], 1).' '.$H.' Z';
        // Участки пути: город → нижняя станция канатки (машина), нижняя станция → отель (канатные дороги).
        $legs = $n >= 3 ? [
            ['car', __('access.leg_car'), __('access.leg_car_meta'), $fmt($levels[0]['altitude_m']).' → '.$fmt($levels[1]['altitude_m'])],
            ['cable', __('access.leg_cable'), __('access.leg_cable_meta'), $fmt($levels[1]['altitude_m']).' → '.$fmt($levels[$n - 1]['altitude_m'])],
        ] : [];
    }
@endphp
@if ($n > 1 && $max > $min)
    <section class="bg-white">
        <div class="container-site grid items-center gap-8 py-12 md:py-16 lg:grid-cols-2 lg:gap-16">
            <div class="relative mx-auto aspect-[13/8] w-full max-w-[560px] overflow-hidden rounded-3xl bg-forest-deep lg:mx-0">
                <x-site.media :src="$hotel->gallery[0] ?? $hotel->hero_image" alt="" round="" class="absolute inset-0 opacity-40" />
                <div class="absolute inset-0 bg-gradient-to-t from-forest-deep/85 via-forest/50 to-forest/20"></div>
                <p class="absolute left-4 top-4 rounded-full bg-white/15 px-3 py-1 text-[13px] text-white backdrop-blur-sm">{{ __('access.altitude') }}</p>
                <svg viewBox="0 0 {{ $W }} {{ $H }}" preserveAspectRatio="none" class="absolute inset-0 h-full w-full" aria-hidden="true">
                    <defs>
                        <linearGradient id="alt-fill" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#fff" stop-opacity=".22"/><stop offset="1" stop-color="#fff" stop-opacity="0"/></linearGradient>
                    </defs>
                    <path d="{{ $area }}" fill="url(#alt-fill)"/>
                    <path d="{{ $d }}" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" vector-effect="non-scaling-stroke"/>
                </svg>
                @foreach ($pts as $i => $p)
                    @php $top = $i === $n - 1; $above = $i % 2 === 1; $c = $top ? '#F0A877' : '#fff'; @endphp
                    <span class="absolute size-3.5 -translate-x-1/2 -translate-y-1/2 rounded-full" style="left: {{ round($p['x'] / $W * 100, 2) }}%; top: {{ round($p['y'] / $H * 100, 2) }}%; background: {{ $c }}; box-shadow: 0 0 0 6px {{ $c }}40"></span>
                    <div class="absolute w-24 -translate-x-1/2 text-center text-white sm:w-28" style="left: {{ round($p['x'] / $W * 100, 2) }}%; top: {{ round($p['y'] / $H * 100, 2) }}%; {{ $above ? 'transform: translate(-50%, calc(-100% - 14px));' : 'margin-top: 14px;' }}">
                        <p class="text-[13px] font-bold leading-tight sm:text-sm">{{ $p['name'] }}</p>
                        <p class="text-[12px] leading-tight {{ $top ? 'text-[#F0A877]' : 'text-white/80' }}">{{ $fmt($p['alt']) }} {{ __('access.m') }}</p>
                    </div>
                @endforeach
            </div>

            <div class="flex flex-col gap-6">
                <div class="flex flex-col gap-3">
                    <h2 class="font-display text-2xl md:text-3xl">{{ __('site.hotel.levels_title') }}</h2>
                    <p class="max-w-xl text-base leading-relaxed text-slate">{{ __('site.hotel.levels_text', ['m' => $fmt($max - $min)]) }}</p>
                </div>
                @if ($legs)
                    <ol class="flex flex-col divide-y divide-mist border-y border-mist">
                        @foreach ($legs as [$icon, $title, $meta, $alt])
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
                @endif
            </div>
        </div>
    </section>
@endif

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
        <dl class="grid grid-cols-2 border-t border-white/15 lg:grid-cols-1 lg:border-l lg:border-t-0">
            @foreach ([
                ['3 200 '.__('access.m'), __('access.stat_altitude')],
                ['≈ '.ResortMap::ONE_WAY_MINUTES.' '.__('map.min'), __('access.stat_time')],
            ] as [$value, $label])
                <div class="flex flex-col justify-start gap-1 border-white/15 px-4 py-5 md:p-8 [&:not(:first-child)]:border-l lg:[&:not(:first-child)]:border-l-0 lg:[&:not(:first-child)]:border-t">
                    <dt class="whitespace-nowrap font-display text-xl leading-none text-ember-light sm:text-2xl md:text-4xl">{{ $value }}</dt>
                    <dd class="text-[13px] leading-snug text-white/70">{{ $label }}</dd>
                </div>
            @endforeach
        </dl>
    </div>
</section>

<section class="container-site pb-0 pt-10 md:pt-16">
    @php
        $cableNote = trim((string) \App\Models\Setting::get('cable.hours_note', ''));
        $lifts = [
            [__('map.tags.station_medeu').' → '.__('map.tags.station_shymbulak'), \App\Models\Setting::get('cable.hours_medeu') ?: '09:00–18:00'],
            ['Combi-1', \App\Models\Setting::get('cable.hours_combi1') ?: '09:00–17:00'],
            ['Combi-2', \App\Models\Setting::get('cable.hours_combi2') ?: '09:00–16:30'],
        ];
        $endOf = fn (string $h, string $fallback) => preg_match('/(\d{1,2}[:.]\d{2})\s*$/u', $h, $m) ? str_replace('.', ':', $m[1]) : $fallback;
        $dayReserve = 0;      // запас днём, мин (в тексте не упоминается)
        $eveningReserve = 0;  // подъём для гостей: быть на Медеу к последней посадке (закрытие − 30 мин)
        $medeuLast = \Illuminate\Support\Carbon::createFromFormat('H:i', $endOf((string) $lifts[0][1], '18:00'))->subMinutes(30 + $eveningReserve);
        $dayLast = \Illuminate\Support\Carbon::createFromFormat('H:i', $endOf((string) $lifts[2][1], '16:30'))->subMinutes(30 + 55 + $dayReserve);
        $dayLast->setTime($dayLast->hour, intdiv($dayLast->minute, 15) * 15);
        $guestFrom = \App\Models\Setting::get('cable.guest_from') ?: '18:00';
        $guestTo = \App\Models\Setting::get('cable.guest_to') ?: '18:30';
    @endphp
    <h2 class="font-display text-2xl md:text-3xl">{{ __('access.hours_title') }}</h2>
    <p class="mt-3 max-w-2xl text-base leading-relaxed text-slate md:text-lg">{{ __('access.hours_text') }}</p>
    {{-- На компьютере: слева режим работы, справа — когда быть на станции и ранний заезд (без пустого места). --}}
    <div class="mt-8 grid gap-4 lg:grid-cols-2">
    <div class="flex flex-col gap-4 rounded-3xl border border-mist p-6 md:p-10">
        <span class="flex h-11 w-11 items-center justify-center rounded-full bg-sand text-ember">{{ site_icon('cable', 'h-6 w-6') }}</span>
        <p class="eyebrow mt-2">{{ __('access.hours_standard') }}</p>
        <ul class="divide-y divide-mist">
            @foreach ($lifts as [$lname, $lhours])
                <li class="flex items-baseline justify-between gap-4 py-3.5">
                    <span class="text-base md:text-lg">{{ $lname }}</span>
                    <span class="whitespace-nowrap font-display text-2xl">{{ $lhours }}</span>
                </li>
            @endforeach
        </ul>
        <p class="text-sm leading-relaxed text-stone">{{ $cableNote !== '' ? $cableNote.'. ' : '' }}{{ __('access.hours_last') }}</p>
    </div>
    <div class="flex flex-col gap-4">
        <div class="flex flex-col gap-3 rounded-3xl border border-mist p-6 md:p-8">
            <p class="eyebrow">{{ __('access.plan_title') }}</p>
            <ul class="flex flex-col gap-3 text-base leading-relaxed text-slate">
                <li><span class="font-display text-2xl text-ink">{{ __('access.plan_until', ['time' => $dayLast->format('H:i')]) }}</span> — {{ __('access.plan_day') }}</li>
            </ul>
            <p class="text-sm leading-relaxed text-stone">{{ __('access.plan_note') }}</p>
        </div>
        <div class="flex flex-col gap-2 rounded-3xl bg-forest p-6 text-white md:p-8 lg:flex-1">
            <p class="eyebrow-light">{{ __('access.early_title') }}</p>
            <p class="text-base leading-relaxed text-white/90">{{ __('access.early_text') }}</p>
        </div>
    </div>
    </div>
    <div class="mt-6 flex flex-col gap-2 border-l-2 border-mist pl-4 text-base leading-relaxed text-slate">
        <p><strong class="font-bold text-ink">{{ __('access.hours_guest') }}.</strong> {{ __('access.hours_guest_text') }}</p>
        <ul class="flex flex-col gap-0.5">
            <li>{{ __('access.guest_depart') }}: <strong class="whitespace-nowrap font-bold text-ink">{{ $guestFrom }}–{{ $guestTo }}</strong></li>
            <li>{{ __('access.guest_be_at') }}: <strong class="font-bold text-ink">{{ __('access.plan_until', ['time' => $medeuLast->format('H:i')]) }}</strong></li>
        </ul>
        <p>{{ __('access.hours_guest_arrive', ['time' => $dayLast->format('H:i')]) }}</p>
    </div>
</section>
<section class="container-site py-10 md:py-16">
    <div class="grid items-stretch gap-10 lg:grid-cols-2 lg:gap-16">
    <div>
    <h2 class="font-display text-2xl md:text-3xl">{{ __('access.route_title') }}</h2>
    <p class="mt-3 max-w-2xl text-base leading-relaxed text-slate">{{ __('access.leg_car') }}: {{ __('access.leg_car_meta') }}.</p>
    <ol class="mt-5 max-w-2xl">
        @foreach ($stations as $i => $s)
            {{-- станция --}}
            <li class="relative flex items-center gap-4">
                <span class="relative z-10 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-ember text-[15px] text-white">{{ $i + 1 }}</span>
                <p class="text-xl leading-snug">{{ __('map.names.station_'.$s['key']) }}</p>
            </li>
            {{-- участок между станциями: сколько едем на канатной дороге --}}
            @if ($s['next'])
                <li class="flex gap-4" aria-label="{{ __('access.ride_label') }}">
                    <span class="flex w-9 shrink-0 justify-center" aria-hidden="true"><span class="w-0.5 bg-ember/40"></span></span>
                    <div class="my-2 flex flex-1 items-center gap-4 rounded-2xl bg-ember/10 px-4 py-3.5">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white text-ember">{{ site_icon('cable', 'h-6 w-6') }}</span>
                        <div class="flex flex-col gap-0.5">
                            <p class="text-[13px] uppercase tracking-[0.14em] text-stone">{{ __('access.ride_label') }}</p>
                            <p class="font-display text-2xl leading-none text-ember">≈ {{ $s['next'] }} {{ __('map.min') }}</p>
                            <p class="text-[15px] text-slate">{{ __('access.ride_to_name', ['name' => __('map.tags.station_'.$stations[$i + 1]['key'])]) }}</p>
                        </div>
                    </div>
                </li>
            @endif
        @endforeach
        <li class="flex gap-4" aria-hidden="true"><span class="flex w-9 shrink-0 justify-center"><span class="my-1 h-6 w-0.5 bg-ember/40"></span></span></li>
        <li class="flex items-center gap-4">
            <span class="relative z-10 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-forest text-white">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" aria-hidden="true"><path d="M3 11l9-7 9 7v9H3z"/><path d="M10 20v-6h4v6"/></svg>
            </span>
            <div class="flex flex-col">
                <p class="text-xl leading-snug">{{ $hotel->t('name') }}</p>
                <p class="text-[15px] text-stone">{{ __('access.at_hotel') }}</p>
            </div>
        </li>
    </ol>
    <p class="mt-6 max-w-2xl text-[15px] leading-relaxed text-stone">{{ __('access.ride_total', ['min' => ResortMap::ONE_WAY_MINUTES, 'unit' => __('map.min')]) }}</p>
    </div>

    {{-- Калькулятор прибытия: успеваете ли подняться стандартными канатками (только со станции Медеу). --}}
    @php
        $mins = fn ($c) => $c->hour * 60 + $c->minute;
        $wa = \App\Models\Setting::get('contacts.whatsapp');
        $writeUrl = $wa ? 'https://wa.me/'.preg_replace('/\D/', '', $wa) : route('contact');
    @endphp
    <div class="flex flex-col">
    <h2 class="font-display text-2xl md:text-3xl">{{ __('access.calc_title') }}</h2>
    <aside id="arrival-calc" class="mt-8 flex flex-1 flex-col gap-6 rounded-3xl border border-mist p-6 md:p-8"
           data-std-last="{{ $mins($dayLast) }}" data-medeu-last="{{ $mins($medeuLast) }}" data-total="{{ ResortMap::ONE_WAY_MINUTES }}"
           data-from="{{ $guestFrom }}" data-to="{{ $guestTo }}"
           data-msg="{{ json_encode(['ok' => __('access.calc_ok'), 'guest' => __('access.calc_guest'), 'no' => __('access.calc_no')], JSON_UNESCAPED_UNICODE) }}">
        <p class="text-base leading-relaxed text-slate">{{ __('access.calc_text') }}</p>

        <div class="mt-4">
            <label for="calc-time" class="flex items-baseline justify-between gap-4 text-base text-slate">
                <span>{{ __('access.calc_label') }}</span>
                <output id="calc-out" class="font-display text-4xl leading-none text-ink">12:00</output>
            </label>
            <input id="calc-time" type="range" min="540" max="1080" step="15" value="720" class="calc-range mt-5 w-full">
            <div class="mt-1 flex justify-between text-sm text-stone" aria-hidden="true"><span>09:00</span><span>12:00</span><span>15:00</span><span>18:00</span></div>
        </div>

        <div id="calc-result" class="flex min-h-[11.5rem] items-start gap-3 rounded-2xl bg-sand p-5 text-base leading-relaxed text-ink" aria-live="polite">
            <span id="calc-dot" class="mt-2 size-2.5 shrink-0 rounded-full bg-forest" aria-hidden="true"></span>
            <div>
                <p id="calc-text"></p>
                <a id="calc-write" href="{{ $writeUrl }}" hidden class="btn-primary mt-4 inline-flex">{{ __('access.calc_write') }}</a>
            </div>
        </div>
        <p class="mt-auto text-sm leading-relaxed text-stone">{{ __('access.calc_hint') }}</p>
    </aside>
    </div>
    </div>
    <script>
        (function () {
            var box = document.getElementById('arrival-calc'); if (!box) return;
            var d = box.dataset, msg = JSON.parse(d.msg), std = +d.stdLast, last = +d.medeuLast, total = +d.total;
            var rng = document.getElementById('calc-time'), out = document.getElementById('calc-out'),
                dot = document.getElementById('calc-dot'), txt = document.getElementById('calc-text'), wr = document.getElementById('calc-write');
            function f(m) { m = Math.round(m); return ('0' + Math.floor(m / 60) % 24).slice(-2) + ':' + ('0' + m % 60).slice(-2); }
            function upd() {
                var t = +rng.value, state, text;
                out.textContent = f(t);
                if (t <= std) { state = 'ok'; text = msg.ok.replace(':time', f(t + total)); }
                else if (t <= last) { state = 'guest'; text = msg.guest.replace(':from', d.from).replace(':to', d.to); }
                else { state = 'no'; text = msg.no.replace(':time', f(last)); }
                txt.textContent = text;
                wr.hidden = state !== 'guest';
                dot.className = 'mt-2 size-2.5 shrink-0 rounded-full ' + (state === 'ok' ? 'bg-forest' : state === 'guest' ? 'bg-ember' : 'bg-stone');
                rng.style.setProperty('--p', ((t - rng.min) / (rng.max - rng.min) * 100) + '%');
            }
            rng.addEventListener('input', upd); upd();
        })();
    </script>

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
        </div>
    </div>
</section>



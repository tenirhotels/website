{{-- «Только по канатной дороге»: маршрут с временем и иллюстрированная карта курорта (V1). --}}
@props(['hotel'])
@php
    use App\Support\ResortMap;

    // Версия граней панорамы = сумма размеров файлов: при замене снимков адрес меняется и кэш браузера/Cloudflare обходится.
    $panoV = fn ($k) => dechex(array_sum(array_map(fn ($f) => (int) @filesize(public_path("panoramas/$k/$f.jpg")), str_split('lfrbud'))));
    $panoList = collect(['medeu', 'shymbulak', 'combi1'])
        ->map(fn ($k) => ['key' => $k, 'title' => __('map.names.station_'.$k)])
        ->push(['key' => 'tenir', 'title' => $hotel->t('name')])
        ->filter(fn ($p) => is_file(public_path('panoramas/'.$p['key'].'/f.jpg')))->values();
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
            ['M-1', __('map.tags.station_medeu').' ↔ '.__('map.tags.station_shymbulak'), \App\Models\Setting::get('cable.hours_medeu') ?: '09:00–18:00'],
            ['K-1', __('map.tags.station_shymbulak').' ↔ '.__('map.tags.station_combi1'), \App\Models\Setting::get('cable.hours_combi1') ?: '09:00–17:00'],
            ['K-2', __('map.tags.station_combi1').' ↔ '.__('map.tags.station_combi2'), \App\Models\Setting::get('cable.hours_combi2') ?: '09:00–16:30'],
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
    <p class="mt-3 max-w-4xl text-base leading-relaxed text-slate md:text-lg">{{ __('access.hours_text') }}</p>
    {{-- На компьютере: слева режим работы, справа — когда быть на станции и ранний заезд (без пустого места). --}}
    <div class="mt-8 grid gap-4 lg:grid-cols-2">
    <div class="flex flex-col gap-4 rounded-3xl border border-mist p-6 md:p-10">
        <span class="flex h-11 w-11 items-center justify-center rounded-full bg-sand text-ember">{{ site_icon('cable', 'h-6 w-6') }}</span>
        <p class="eyebrow mt-2">{{ __('access.hours_standard') }}</p>
        <p class="-mt-2 text-sm leading-relaxed text-stone">{{ __('access.hours_lines') }}</p>
        <ul class="divide-y divide-mist">
            @foreach ($lifts as [$lcode, $lname, $lhours])
                <li class="flex items-baseline justify-between gap-4 py-3.5">
                    <span class="flex items-center gap-3 text-base md:text-lg"><span class="inline-flex min-w-[3.25rem] justify-center rounded-full bg-ember/10 px-2.5 py-1 text-sm font-semibold leading-none text-ember" title="{{ __('access.line', ['code' => $lcode]) }}">{{ $lcode }}</span>{{ $lname }}</span>
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
                @if (is_file(public_path('panoramas/'.$s['key'].'/f.jpg')))
                    <button type="button" data-pano="{{ $s['key'] }}" data-v="{{ $panoV($s['key']) }}" data-title="{{ __('map.names.station_'.$s['key']) }}" aria-label="{{ __('access.pano_open') }}: {{ __('map.names.station_'.$s['key']) }}" class="ml-1 inline-flex shrink-0 items-center gap-1.5 rounded-full border border-ember/50 px-3 py-1 text-[13px] leading-none text-ember transition hover:bg-ember/10">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" class="h-4 w-4" aria-hidden="true"><ellipse cx="12" cy="12" rx="9" ry="4"/><path d="M12 3v18M5 7.5C7 9 17 9 19 7.5"/></svg>360°
                    </button>
                @endif
            </li>
            {{-- участок между станциями: сколько едем на канатной дороге --}}
            @if ($s['next'])
                <li class="flex gap-4" aria-label="{{ __('access.ride_label') }}">
                    <span class="flex w-9 shrink-0 justify-center" aria-hidden="true"><span class="w-0.5 bg-ember/40"></span></span>
                    <div class="my-2 flex flex-1 items-center gap-4 overflow-hidden rounded-2xl bg-ember/10 px-4 py-3.5">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white text-ember">{{ site_icon('cable', 'h-6 w-6') }}</span>
                        <div class="flex min-w-0 flex-1 flex-col gap-0.5">
                            <p class="text-[13px] uppercase tracking-[0.14em] text-stone">{{ __('access.line', ['code' => $s['lift']]) }} · {{ __('access.ride_label') }}</p>
                            <p class="font-display text-2xl leading-none text-ember">≈ {{ $s['next'] }} {{ __('map.min') }}</p>
                            <p class="text-[15px] text-slate">{{ __('access.ride_to_name', ['name' => __('map.names.station_'.$stations[$i + 1]['key'])]) }}</p>
                        </div>
                        @php
                            $photoPath = \App\Models\Setting::get('cable.photo_'.$s['key']);
                            $photo = media($photoPath);
                        @endphp
                        @if ($photo)
                            <button type="button" data-zoom="{{ $photo }}" data-alt="{{ __('map.names.station_'.$s['key']) }}" aria-label="{{ __('access.photo_zoom') }}: {{ __('map.names.station_'.$s['key']) }}" class="group relative -my-3.5 -mr-4 w-28 shrink-0 cursor-zoom-in self-stretch sm:w-40">
                                <img src="{{ $photo }}" srcset="{{ media_srcset($photoPath) }}" sizes="160px" alt="" loading="lazy" decoding="async" class="absolute inset-0 h-full w-full object-cover transition group-hover:brightness-90">
                                <span class="absolute bottom-2 right-2 flex h-7 w-7 items-center justify-center rounded-full bg-white/90 text-ink" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" class="h-4 w-4"><circle cx="11" cy="11" r="6"/><path d="M20 20l-4.5-4.5M11 8.5v5M8.5 11h5"/></svg></span>
                            </button>
                        @endif
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
            @if (is_file(public_path('panoramas/tenir/f.jpg')))
                <button type="button" data-pano="tenir" data-v="{{ $panoV('tenir') }}" data-title="{{ $hotel->t('name') }}" aria-label="{{ __('access.pano_open') }}: {{ $hotel->t('name') }}" class="ml-1 inline-flex shrink-0 items-center gap-1.5 rounded-full border border-ember/50 px-3 py-1 text-[13px] leading-none text-ember transition hover:bg-ember/10">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" class="h-4 w-4" aria-hidden="true"><ellipse cx="12" cy="12" rx="9" ry="4"/><path d="M12 3v18M5 7.5C7 9 17 9 19 7.5"/></svg>360°
                    </button>
            @endif
        </li>
    </ol>
    <p class="mt-6 max-w-2xl text-[15px] leading-relaxed text-stone">{{ __('access.ride_total', ['min' => ResortMap::ONE_WAY_MINUTES, 'unit' => __('map.min')]) }}</p>
    </div>

    {{-- Калькулятор прибытия: успеваете ли подняться стандартными канатками (только со станции Медеу). --}}
    @php
        $mins = fn ($c) => $c->hour * 60 + $c->minute;
        // Смещения посадок от выезда с Медеу: участки + равные пересадки (сумма = время в пути в одну сторону).
        $legs = $stations->pluck('next')->filter()->values();
        $transfer = ($legs->count() > 1) ? (ResortMap::ONE_WAY_MINUTES - $legs->sum()) / ($legs->count() - 1) : 0;
        $offsets = [0, $legs[0] + $transfer, $legs[0] + $transfer + $legs[1] + $transfer, ResortMap::ONE_WAY_MINUTES];
        $stationNames = $stations->map(fn ($st) => __('map.tags.station_'.$st['key']))->values()->all();
        $steps = [
            'board' => __('access.st_board'), 'change' => __('access.st_change'), 'arrive' => __('access.st_arrive'),
            'std' => __('access.st_std'), 'last' => __('access.st_last'), 'special' => __('access.st_special'),
            'until' => __('access.plan_until'), 'hotel' => $hotel->t('name'),
        ];
        $wa = \App\Models\Setting::get('contacts.whatsapp');
        $writeUrl = $wa ? 'https://wa.me/'.preg_replace('/\D/', '', $wa) : route('contact');
    @endphp
    <div class="flex flex-col">
    <h2 class="font-display text-2xl md:text-3xl">{{ __('access.calc_title') }}</h2>
    <aside id="arrival-calc" class="mt-8 flex flex-1 flex-col gap-6 rounded-3xl border border-mist p-6 md:p-8"
           data-std-last="{{ $mins($dayLast) }}" data-medeu-last="{{ $mins($medeuLast) }}" data-total="{{ ResortMap::ONE_WAY_MINUTES }}"
           data-from="{{ $guestFrom }}" data-to="{{ $guestTo }}"
           data-offsets="{{ json_encode($offsets) }}" data-names="{{ json_encode($stationNames, JSON_UNESCAPED_UNICODE) }}" data-steps="{{ json_encode($steps, JSON_UNESCAPED_UNICODE) }}"
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

        <div id="calc-result" class="flex min-h-[7.5rem] items-start gap-3 rounded-2xl bg-sand p-5 text-base leading-relaxed text-ink" aria-live="polite">
            <span id="calc-dot" class="mt-2 size-2.5 shrink-0 rounded-full bg-forest" aria-hidden="true"></span>
            <p id="calc-text"></p>
        </div>
        {{-- Ход подъёма по времени; для остальных вариантов — ключевые времена. Высота фиксирована, чтобы ничего не прыгало. --}}
        <div class="min-h-[12rem]">
            <ol id="calc-steps" class="divide-y divide-mist border-t border-mist"></ol>
            <a id="calc-write" href="{{ $writeUrl }}" hidden class="btn-primary mt-5 inline-flex">{{ __('access.calc_write') }}</a>
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
                dot = document.getElementById('calc-dot'), txt = document.getElementById('calc-text'), wr = document.getElementById('calc-write'), ol = document.getElementById('calc-steps'),
                off = JSON.parse(d.offsets), names = JSON.parse(d.names), st = JSON.parse(d.steps);
            function f(m) { m = Math.round(m); return ('0' + Math.floor(m / 60) % 24).slice(-2) + ':' + ('0' + m % 60).slice(-2); }
            function upd() {
                var t = +rng.value, state, text;
                out.textContent = f(t);
                if (t <= std) { state = 'ok'; text = msg.ok.replace(':time', f(t + total)); }
                else if (t <= last) { state = 'guest'; text = msg.guest.replace(':from', d.from).replace(':to', d.to); }
                else { state = 'no'; text = msg.no.replace(':time', f(last)); }
                txt.textContent = text;
                var until = function (m) { return st.until.replace(':time', f(m)); }, rows;
                if (state === 'ok') rows = [[f(t), names[0], st.board], [f(t + off[1]), names[1], st.change], [f(t + off[2]), names[2], st.change], [f(t + off[3]), names[3] + ' \u00b7 ' + st.hotel, st.arrive]];
                else if (state === 'guest') rows = [[until(last), names[0], st.last], [d.from + '\u2013' + d.to, names[1], st.special]];
                else rows = [[until(std), names[0], st.std], [until(last), names[0], st.last + ' \u00b7 ' + st.special.toLowerCase()]];
                ol.textContent = '';
                rows.forEach(function (r) {
                    var li = document.createElement('li'); li.className = 'flex items-baseline gap-4 py-2.5';
                    var tm = document.createElement('span'); tm.className = (state === 'ok' ? 'w-[4.25rem]' : 'w-32') + ' shrink-0 whitespace-nowrap font-display text-xl text-ink'; tm.textContent = r[0];
                    var bx = document.createElement('div'), n = document.createElement('p'), sb = document.createElement('p');
                    n.className = 'text-base leading-snug text-ink'; n.textContent = r[1]; sb.className = 'text-sm text-stone'; sb.textContent = r[2];
                    bx.appendChild(n); bx.appendChild(sb); li.appendChild(tm); li.appendChild(bx); ol.appendChild(li);
                });
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

<dialog id="photo-zoom" class="m-auto max-h-[92vh] w-[min(94vw,1100px)] overflow-visible bg-transparent p-0 backdrop:bg-black/80">
    <img id="photo-zoom-img" src="" alt="" class="max-h-[88vh] w-full rounded-2xl object-contain">
    <button type="button" id="photo-zoom-close" aria-label="{{ __('access.photo_close') }}" class="absolute -top-3 right-0 flex h-10 w-10 items-center justify-center rounded-full bg-white text-ink shadow md:-right-3">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="h-5 w-5" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
    </button>
</dialog>
<script>
(() => {
    const d = document.getElementById('photo-zoom'), img = document.getElementById('photo-zoom-img');
    if (!d || !d.showModal) return;
    document.querySelectorAll('[data-zoom]').forEach(b => b.addEventListener('click', () => {
        img.src = b.dataset.zoom; img.alt = b.dataset.alt || ''; d.showModal();
    }));
    d.addEventListener('click', e => { if (e.target !== img) d.close(); });
    document.getElementById('photo-zoom-close').addEventListener('click', () => d.close());
})();
</script>

<dialog id="pano-modal" class="m-auto h-[min(86vh,760px)] w-[min(96vw,1200px)] overflow-visible bg-transparent p-0 backdrop:bg-black/80">
    <div class="relative h-full w-full overflow-hidden rounded-2xl bg-[#1b1f1d]">
        <div id="pano-view" class="h-full w-full"></div>
        <nav class="absolute bottom-3 left-3 right-3 z-10 flex gap-2 overflow-x-auto pb-1 sm:justify-center" aria-label="{{ __('access.pano_open') }}">
            @foreach ($panoList as $p)
                <button type="button" data-pano-go="{{ $p['key'] }}" data-v="{{ $panoV($p['key']) }}" class="shrink-0 whitespace-nowrap rounded-full bg-black/50 px-4 py-2 text-sm text-white transition hover:bg-black/70 aria-[current=true]:bg-white aria-[current=true]:text-ink">{{ $p['title'] }}</button>
            @endforeach
        </nav>
        <p class="pointer-events-none absolute left-1/2 top-3 z-10 -translate-x-1/2 whitespace-nowrap rounded-full bg-black/50 px-4 py-2 text-xs text-white">{{ __('access.pano_hint') }}</p>
    </div>
    <button type="button" id="pano-close" aria-label="{{ __('access.photo_close') }}" class="absolute -top-3 right-0 z-20 flex h-10 w-10 items-center justify-center rounded-full bg-white text-ink shadow md:-right-3">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="h-5 w-5" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
    </button>
</dialog>
<style>#pano-view.pnlm-container{background:#1b1f1d}</style>
<script>
(() => {
    const d = document.getElementById('pano-modal');
    if (!d || !d.showModal) return;
    const V = '/vendor/pannellum/', FACES = ['l', 'f', 'r', 'b', 'u', 'd'];
    let viewer = null, loading = null;
    // Pannellum грузим только при первом открытии панорамы.
    const lib = () => loading ??= new Promise((ok, fail) => {
        const l = document.createElement('link'); l.rel = 'stylesheet'; l.href = V + 'pannellum.css'; document.head.appendChild(l);
        const s = document.createElement('script'); s.src = V + 'pannellum.js'; s.onload = ok; s.onerror = () => { loading = null; fail(); };
        document.head.appendChild(s);
    });
    const close = () => { d.close(); };
    d.addEventListener('close', () => { if (viewer) { viewer.destroy(); viewer = null; } });
    d.addEventListener('click', e => { if (e.target === d) close(); });
    document.getElementById('pano-close').addEventListener('click', close);
    const chips = [...document.querySelectorAll('[data-pano-go]')];
    const show = async (key, v) => {
        chips.forEach(c => c.setAttribute('aria-current', c.dataset.panoGo === key ? 'true' : 'false'));
        try { await lib(); } catch (e) { return; }
        if (viewer) viewer.destroy();
        viewer = pannellum.viewer('pano-view', {
            type: 'cubemap',
            cubeMap: FACES.map(f => '/panoramas/' + key + '/' + f + '.jpg?v=' + (v || '')),
            autoLoad: true, showControls: true, showZoomCtrl: true, showFullscreenCtrl: true,
            mouseZoom: true, draggable: true, hfov: 100, minHfov: 40, maxHfov: 120, pitch: 0, yaw: 0,
        });
    };
    document.querySelectorAll('[data-pano]').forEach(b => b.addEventListener('click', () => {
        d.showModal();
        show(b.dataset.pano, b.dataset.v);
    }));
    chips.forEach(c => c.addEventListener('click', () => show(c.dataset.panoGo, c.dataset.v)));
})();
</script>

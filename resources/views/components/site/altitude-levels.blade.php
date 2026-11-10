@props(['hotel'])
@php
    $levels = collect($hotel->levels ?? [])->filter(fn ($l) => ($l['altitude_m'] ?? 0) > 0)->sortBy('altitude_m')->values();
    $max = (int) $levels->max('altitude_m');
    $diff = $levels->count() > 1 ? (int) $levels->last()['altitude_m'] - (int) $levels->first()['altitude_m'] : 0;
    $fmt = fn ($m) => number_format((int) $m, 0, ',', "\u{00A0}").' '.__('access.m');
@endphp
@if ($levels->count() > 1 && $max > 0)
    <section class="container-site border-t border-mist py-12 md:py-16">
        <h2 class="font-display text-2xl md:text-3xl">{{ __('site.hotel.levels_title') }}</h2>
        @if ($diff)<p class="mt-3 max-w-2xl text-base leading-relaxed text-slate">{{ __('site.hotel.levels_text', ['m' => number_format($diff, 0, ',', "\u{00A0}")]) }}</p>@endif
        <ol class="mt-8 flex max-w-3xl flex-col gap-3">
            @foreach ($levels->reverse() as $level)
                @php $top = $loop->first; @endphp
                <li class="grid grid-cols-[7.5rem_1fr_auto] items-center gap-3 sm:grid-cols-[11rem_1fr_auto] sm:gap-5">
                    <span class="text-[15px] leading-tight {{ $top ? 'font-medium text-ink' : 'text-slate' }}">{{ \App\Support\Locales::pick($level['name'] ?? []) }}</span>
                    <span class="h-3 rounded-full bg-sand" aria-hidden="true">
                        <span class="block h-3 rounded-full {{ $top ? 'bg-ember' : 'bg-forest/45' }}" style="width: {{ max(4, round($level['altitude_m'] / $max * 100)) }}%"></span>
                    </span>
                    <span class="whitespace-nowrap text-right font-display text-lg {{ $top ? 'text-ember' : 'text-ink' }}">{{ $fmt($level['altitude_m']) }}</span>
                </li>
            @endforeach
        </ol>
    </section>
@endif

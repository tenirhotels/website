{{-- Единственный тип номера: не «карточка в пустой сетке», а полноценная витрина. --}}
@php
    $icons = [
        'size' => '<path d="M4 9V4h5M20 9V4h-5M4 15v5h5M20 15v5h-5"/>',
        'guests' => '<circle cx="9" cy="8" r="3.2"/><path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6"/><path d="M16 5.2a3 3 0 0 1 0 5.6M18 14.4c1.8.8 3 2.6 3 5.6"/>',
        'bed' => '<path d="M3 18V7M3 14h18v4M21 14v-2a3 3 0 0 0-3-3h-7v5"/><circle cx="7" cy="11" r="1.6"/>',
        'view' => '<path d="M2 19l6-9 4 5 3-4 7 8z"/><circle cx="17" cy="6" r="1.6"/>',
        'sauna' => '<path d="M8 4c-1.5 2 1.5 3 0 5M12 4c-1.5 2 1.5 3 0 5M16 4c-1.5 2 1.5 3 0 5M4 14h16v3a3 3 0 0 1-3 3H7a3 3 0 0 1-3-3z"/>',
        'window' => '<rect x="4" y="3" width="16" height="18" rx="1.5"/><path d="M12 3v18M4 12h16"/>',
        'ski' => '<path d="M3 20l9-16 9 16M7.5 13l4.5 2.5 4.5-2.5"/>',
        'module' => '<path d="M3 11l9-7 9 7v9H3z"/><path d="M10 20v-6h4v6"/>',
        'leaf' => '<path d="M5 19c0-8 5-14 15-14 0 10-6 15-14 15M5 19c2-4 5-7 9-9"/>',
    ];
    $pick = function (string $text) {
        $t = mb_strtolower($text);
        return match (true) {
            str_contains($t, 'саун') || str_contains($t, 'sauna') || str_contains($t, 'сауна') => 'sauna',
            str_contains($t, 'окн') || str_contains($t, 'window') || str_contains($t, 'терас') || str_contains($t, 'терез') => 'window',
            str_contains($t, 'ski') || str_contains($t, 'лыж') => 'ski',
            str_contains($t, 'модул') || str_contains($t, 'module') => 'module',
            default => 'leaf',
        };
    };
    $svg = fn (string $k) => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6" aria-hidden="true">'.$icons[$k].'</svg>';
    $facts = collect([
        ['size', __('site.hotel.size'), $room->size_m2 ? $room->size_m2.' м²' : null],
        ['guests', __('site.hotel.guests'), $room->max_guests ? __('site.hotel.up_to_guests', ['count' => $room->max_guests]) : null],
        ['bed', __('site.hotel.bed'), $room->t('bed')],
        ['view', __('site.hotel.view'), $room->t('view')],
    ])->filter(fn ($f) => $f[2]);
    $gallery = collect($room->gallery ?? [])->filter()->values();
    $amenities = $room->amenityList();
@endphp

<section class="container-site py-12 md:py-16 lg:py-20">
    <div class="grid items-center gap-10 lg:grid-cols-2 lg:gap-16">
        <a href="{{ $room->url() }}" class="block aspect-[4/5] overflow-hidden rounded-3xl lg:aspect-[5/6]" tabindex="-1" aria-hidden="true">
            <x-site.media :src="$room->image" :alt="$room->t('name')" round="" />
        </a>

        <div class="flex flex-col gap-7">
            <div class="flex flex-col gap-4">
                <h2 class="font-display text-4xl leading-tight md:text-5xl">{{ $room->t('name') }}</h2>
                <p class="text-lg leading-relaxed text-slate">{{ $room->t('summary') }}</p>
            </div>

            @if ($facts->isNotEmpty())
                <dl class="grid grid-cols-2 gap-x-6 gap-y-5 border-y border-mist py-6">
                    @foreach ($facts as [$k, $label, $value])
                        <div class="flex items-start gap-3">
                            <span class="mt-0.5 text-ember">{!! $svg($k) !!}</span>
                            <div><dt class="field-label mb-0.5">{{ $label }}</dt><dd class="text-[15px]">{{ $value }}</dd></div>
                        </div>
                    @endforeach
                </dl>
            @endif

            <div class="prose-tenir">{!! $room->t('description') !!}</div>

            <div class="flex flex-wrap items-center gap-x-8 gap-y-4">
                <a href="{{ $room->bookingUrl() }}" class="btn-primary">{{ __('site.hotel.book_room') }}</a>
                @if ($room->price_from)
                    <p class="text-slate">{{ __('site.hotel.from') }} <strong class="text-2xl font-normal text-ink">{{ price($room->price_from) }} ₸</strong> / {{ __('site.hotel.per_night') }}</p>
                @endif
            </div>
        </div>
    </div>
</section>

@if ($amenities)
    <section class="bg-white">
        <div class="container-site flex flex-col gap-10 py-12 md:py-16">
            <h2 class="font-display text-3xl md:text-4xl">{{ __('site.hotel.amenities') }}</h2>
            <ul class="grid grid-cols-2 gap-4 md:grid-cols-4">
                @foreach ($amenities as $a)
                    <li class="flex flex-col gap-3 rounded-2xl bg-sand/60 p-5 text-forest">
                        {!! $svg($pick($a)) !!}
                        <span class="text-[15px] leading-snug text-ink">{{ $a }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>
@endif

@if ($gallery->isNotEmpty())
    <section class="container-site py-12 md:py-16 lg:py-20">
        <div class="grid gap-3 {{ $gallery->count() === 1 ? '' : 'grid-cols-2' }} {{ $gallery->count() >= 3 ? 'md:grid-cols-3' : '' }}">
            @foreach ($gallery as $img)
                <div class="aspect-[4/3] overflow-hidden rounded-2xl {{ $gallery->count() === 1 ? 'md:aspect-[21/9]' : '' }}"><x-site.media :src="$img" :alt="$room->t('name')" /></div>
            @endforeach
        </div>
    </section>
@endif

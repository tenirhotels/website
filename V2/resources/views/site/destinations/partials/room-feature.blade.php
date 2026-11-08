{{-- Единственный тип номера: не «карточка в пустой сетке», а полноценная витрина. --}}
@php
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
                <h2 class="font-display text-3xl leading-tight md:text-4xl">{{ $room->t('name') }}</h2>
                <p class="text-lg leading-relaxed text-slate">{{ $room->t('summary') }}</p>
            </div>

            @if ($facts->isNotEmpty())
                <dl class="grid grid-cols-2 gap-x-6 gap-y-5 border-y border-mist py-6">
                    @foreach ($facts as [$k, $label, $value])
                        <div class="flex items-start gap-3">
                            <span class="mt-0.5 text-ember">{{ site_icon($k) }}</span>
                            <div><dt class="field-label mb-0.5">{{ $label }}</dt><dd class="text-base">{{ $value }}</dd></div>
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
            <h2 class="font-display text-2xl md:text-3xl">{{ __('site.hotel.amenities') }}</h2>
            <ul class="grid grid-cols-2 gap-4 md:grid-cols-4">
                @foreach ($amenities as $a)
                    <li class="flex flex-col gap-3 rounded-2xl bg-sand/60 p-5 text-forest">
                        {{ amenity_icon($a) }}
                        <span class="text-base leading-snug text-ink">{{ $a }}</span>
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

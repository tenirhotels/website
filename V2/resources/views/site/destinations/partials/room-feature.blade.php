{{-- Единственный тип номера: не «карточка в пустой сетке», а полноценная витрина. --}}
@php
    $tiles = $room->tiles();
    $amenities = $room->listFeatures();
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

            @if ($tiles)
                <ul class="divide-y divide-mist border-y border-mist">
                            @foreach ($tiles as $tile)
                                <li class="flex items-center gap-4 py-3.5">
                                    <span class="flex h-7 w-7 shrink-0 items-center justify-center text-ember">{{ site_icon($tile['icon'], 'h-6 w-6') }}</span>
                                    <span class="font-display text-2xl leading-tight text-ink md:text-3xl">{{ $tile['text'] }}</span>
                                </li>
                            @endforeach
                        </ul>
            @endif

            <div class="prose-tenir">{!! $room->t('description') !!}</div>

            <div class="flex flex-wrap items-center gap-x-8 gap-y-4">
                <a href="{{ $room->bookingUrl() }}" class="btn-primary">{{ __('site.hotel.book_room') }}</a>
                <a href="{{ $room->url() }}" class="inline-block border-b border-current pb-1.5 text-[13px] uppercase tracking-[0.2em] text-ember hover:text-forest">{{ __('site.hotel.room_details') }} →</a>
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
                        {{ site_icon($a['icon']) }}
                        <span class="text-base leading-snug text-ink">{{ $a['text'] }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>
@endif

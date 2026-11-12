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

        <div class="flex flex-col gap-6">
            <div class="flex flex-col gap-3">
                <h2 class="font-display text-3xl leading-tight md:text-4xl">{{ $room->t('name') }}</h2>
                <p class="text-[17px] leading-relaxed text-slate">{{ $room->t('summary') }}</p>
            </div>

            @if ($tiles)
                <ul class="flex flex-wrap gap-x-6 gap-y-2 border-y border-mist py-3.5">
                    @foreach ($tiles as $tile)
                        <li class="flex items-center gap-2.5 text-[16px] leading-none text-ink">
                            <span class="shrink-0 text-ember">{{ site_icon($tile['icon'], 'h-5 w-5') }}</span>
                            <span>{{ $tile['text'] }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif

            <div class="prose-tenir !text-[16px] !leading-[1.7] [&_p:last-child]:mb-0">{!! $room->t('description') !!}</div>

            @if ($amenities)
                <div class="flex flex-col gap-4">
                    <h3 class="text-[12px] uppercase tracking-[0.2em] text-slate">{{ __('site.hotel.amenities') }}</h3>
                    <ul class="grid grid-cols-1 gap-x-6 gap-y-3 sm:grid-cols-2">
                        @foreach ($amenities as $a)
                            <li class="flex items-center gap-3 text-[15px] leading-snug text-ink">
                                <span class="shrink-0 text-forest">{{ site_icon($a['icon'], 'h-5 w-5') }}</span>
                                <span>{{ $a['text'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

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

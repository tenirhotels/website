<x-layouts.site :seo="$seo" :hotel="$hotel">
    <x-site.page-hero :image="$room->image" :eyebrow="$hotel->t('name')" :title="$room->t('name')" :text="$room->t('summary')" />
    <x-site.hotel-nav :hotel="$hotel" />

    <section class="container-site flex flex-col gap-8 md:gap-14 py-12 md:py-16 lg:py-20 lg:flex-row lg:gap-20">
        <div class="flex flex-[1.5] flex-col gap-10">
            <div class="prose-tenir">{!! $room->t('description') !!}</div>

            @if ($room->gallery)
                <div class="grid grid-cols-2 gap-3">
                    @foreach ($room->gallery as $img)
                        <div class="aspect-[4/3] overflow-hidden rounded-2xl"><x-site.media :src="$img" :alt="$room->t('name')" /></div>
                    @endforeach
                </div>
            @endif

            @if ($amenities = $room->featureList())
                <div>
                    <h2 class="mb-5 font-display text-2xl">{{ __('site.hotel.amenities') }}</h2>
                    <ul class="grid gap-x-8 gap-y-3 text-slate sm:grid-cols-2">
                        @foreach ($amenities as $a)
                            <li class="flex items-start gap-3 border-b border-mist pb-3 leading-snug"><span class="flex h-6 w-6 shrink-0 items-center justify-center text-forest">{{ site_icon($a['icon'], 'h-5 w-5') }}</span><span>{{ $a['text'] }}</span></li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        {{-- Карточка бронирования: закреплена при прокрутке на десктопе --}}
        <aside class="flex-1">
            <div class="sticky top-40 flex flex-col gap-6 bg-white p-8 rounded-3xl">
                <dl class="grid grid-cols-2 gap-5 text-base">
                    @foreach ([
                        __('site.hotel.size') => $room->size_m2 ? $room->size_m2.' м²' : null,
                        __('site.hotel.guests') => $room->max_guests,
                        __('site.hotel.bed') => $room->t('bed'),
                        __('site.hotel.view') => $room->t('view'),
                    ] as $label => $value)
                        @if ($value)
                            <div><dt class="field-label mb-1">{{ $label }}</dt><dd>{{ $value }}</dd></div>
                        @endif
                    @endforeach
                </dl>
                @if ($room->price_from)
                    <p class="border-t border-mist pt-5 text-slate">{{ __('site.hotel.from') }} <strong class="text-3xl font-normal text-ink">{{ price($room->price_from) }} ₸</strong> / {{ __('site.hotel.per_night') }}</p>
                @endif
                <a href="{{ $room->bookingUrl() }}" class="btn-primary w-full">{{ __('site.hotel.book_room') }}</a>
                <p class="text-center text-[13px] text-stone">{{ __('site.booking.best_rate') }}</p>
            </div>
        </aside>
    </section>

    @if ($others->isNotEmpty())
        <section class="bg-white">
            <div class="container-site flex flex-col gap-8 md:gap-12 py-12 md:py-16 lg:py-20">
                <x-site.section-head :title="__('site.hotel.other_rooms')" level="h2" />
                <div class="grid gap-8 md:grid-cols-3">
                    @foreach ($others as $r)
                        @include('site.destinations.partials.room-card', ['room' => $r])
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-layouts.site>

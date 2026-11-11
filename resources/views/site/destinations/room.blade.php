<x-layouts.site :seo="$seo" :hotel="$hotel">
    <x-site.page-hero :image="$room->image" :eyebrow="$hotel->t('name')" :title="$room->t('name')" :text="$room->t('summary')" />
    <x-site.hotel-nav :hotel="$hotel" />

    @php
        $factSources = ['size', 'guests', 'bed', 'view'];
        $all = collect($room->featureList());
        $facts = collect([
            ['icon' => 'size', 'label' => __('site.hotel.size'), 'value' => $room->size_m2 ? $room->size_m2.' м²' : null],
            ['icon' => 'guests', 'label' => __('site.hotel.guests'), 'value' => $room->max_guests ? __('site.hotel.up_to_guests', ['count' => $room->max_guests]) : null],
            ['icon' => 'bed', 'label' => __('site.hotel.bed'), 'value' => $room->t('bed')],
            ['icon' => 'view', 'label' => __('site.hotel.view'), 'value' => $room->t('view')],
        ])->filter(fn ($f) => filled($f['value']));
        $highlights = $all->filter(fn ($f) => $f['pin'] && ! in_array($f['source'], $factSources, true));
        $amenities = $all->reject(fn ($f) => $f['pin'] || in_array($f['source'], $factSources, true));
    @endphp

    <section class="container-site flex flex-col gap-10 py-12 md:py-16 lg:flex-row lg:gap-16 lg:py-20">
        <div class="flex flex-[1.5] flex-col gap-10">
            <div class="prose-tenir !text-[18px] [&_p:last-child]:mb-0">{!! $room->t('description') !!}</div>

            @if ($room->gallery)
                <x-site.gallery :images="$room->gallery" :alt="$room->t('name')" :layout="$room->gallery_layout ?: 'mosaic'" />
            @endif

            @if ($amenities->isNotEmpty())
                <div>
                    <h2 class="mb-5 font-display text-2xl">{{ __('site.hotel.amenities') }}</h2>
                    <ul class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        @foreach ($amenities as $a)
                            <li class="flex items-center gap-4 rounded-2xl bg-white px-4 py-3.5 leading-snug text-ink">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-sand text-ember">{{ site_icon($a['icon'], 'h-5 w-5') }}</span>
                                <span class="text-[16px]">{{ $a['text'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        {{-- Карточка номера: факты, цена, бронь. Закреплена при прокрутке на десктопе --}}
        <aside class="flex-1">
            <div class="sticky top-40 flex flex-col gap-6 rounded-3xl bg-forest-deep p-7 text-white md:p-8">
                <ul class="flex flex-col divide-y divide-white/15">
                    @foreach ($facts as $f)
                        <li class="flex items-center gap-4 py-3.5 first:pt-0">
                            <span class="shrink-0 text-ember-light">{{ site_icon($f['icon'], 'h-6 w-6') }}</span>
                            <span class="flex flex-1 items-baseline justify-between gap-4">
                                <span class="text-[12px] uppercase tracking-[0.18em] text-white/60">{{ $f['label'] }}</span>
                                <span class="text-right font-display text-xl leading-tight">{{ $f['value'] }}</span>
                            </span>
                        </li>
                    @endforeach
                    @foreach ($highlights as $h)
                        <li class="flex items-center gap-4 py-3.5">
                            <span class="shrink-0 text-ember-light">{{ site_icon($h['icon'], 'h-6 w-6') }}</span>
                            <span class="font-display text-xl leading-tight">{{ $h['text'] }}</span>
                        </li>
                    @endforeach
                </ul>

                @if ($room->price_from)
                    <p class="text-white/70">{{ __('site.hotel.from') }} <strong class="font-display text-4xl font-normal text-white">{{ price($room->price_from) }} ₸</strong> / {{ __('site.hotel.per_night') }}</p>
                @endif
                <a href="{{ $room->bookingUrl() }}" class="btn-primary w-full">{{ __('site.hotel.book_room') }}</a>
                <p class="text-center text-[13px] text-white/60">{{ __('site.booking.best_rate') }}</p>
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

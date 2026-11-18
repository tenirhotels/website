<x-layouts.site :seo="$seo" :hotel="$hotel">
    <x-site.page-hero :image="$room->image" :eyebrow="$hotel->t('name')" :title="$room->t('name')" :text="$room->t('summary')" />
    <x-site.hotel-nav :hotel="$hotel" />

    @php
        $factSources = ['size', 'guests', 'bed', 'view'];
        $all = collect($room->featureList());
        $facts = collect([
            ['icon' => 'size', 'label' => __('site.hotel.size'), 'value' => $room->size_m2 ? $room->size_m2.' м²' : null, 'sub' => null],
            ['icon' => 'guests', 'label' => __('site.hotel.guests'), 'value' => $room->guestsLabel(false), 'sub' => $room->guestsSplit()],
            ['icon' => 'bed', 'label' => __('site.hotel.bed'), 'value' => $room->t('bed'), 'sub' => null],
            ['icon' => 'view', 'label' => __('site.hotel.view'), 'value' => $room->t('view'), 'sub' => null],
        ])->filter(fn ($f) => filled($f['value']))->values();
        $highlights = $all->filter(fn ($f) => $f['pin'] && ! in_array($f['source'], $factSources, true))->values();
        $amenities = $all->reject(fn ($f) => $f['pin'] || in_array($f['source'], $factSources, true));
        $groups = $amenities->groupBy(fn ($a) => amenity_group($a['icon']))
            ->sortBy(fn ($g, $k) => array_search($k, ['rest', 'bath', 'tech', 'food', 'active', 'other'], true));
    @endphp

    {{-- Полоса параметров: парит на стыке шапки и страницы --}}
    @if ($facts->isNotEmpty())
        <div class="container-site relative z-10 -mt-8 md:-mt-14">
            <dl class="grid grid-cols-2 gap-px [&>*:last-child:nth-child(odd)]:col-span-2 md:[&>*:last-child:nth-child(odd)]:col-span-1 overflow-hidden rounded-3xl bg-mist shadow-[0_20px_50px_-24px_rgba(22,33,27,.35)] {{ ['', 'md:grid-cols-1', 'md:grid-cols-2', 'md:grid-cols-3', 'md:grid-cols-4'][min($facts->count(), 4)] }}">
                @foreach ($facts as $f)
                    <div class="flex flex-col gap-3 bg-white px-6 py-6 md:px-8 md:py-7">
                        <dt class="flex items-center gap-2 text-[12px] uppercase tracking-[0.16em] text-stone">
                            <span class="text-ember">{{ site_icon($f['icon'], 'h-5 w-5') }}</span>{{ $f['label'] }}
                        </dt>
                        <dd class="flex flex-col gap-1">
                            <span class="font-display text-[24px] leading-tight text-forest md:text-[28px]">{{ $f['value'] }}</span>
                            @if ($f['sub'])<span class="text-[14px] leading-snug text-stone">{{ $f['sub'] }}</span>@endif
                        </dd>
                    </div>
                @endforeach
            </dl>
        </div>
    @endif

    <section class="container-site flex flex-col gap-12 py-12 md:py-16 lg:flex-row lg:gap-16 lg:py-20">
        <div class="flex min-w-0 flex-[1.7] flex-col gap-14">
            <div class="prose-tenir !text-[18px] [&_p:last-child]:mb-0">{!! $room->t('description') !!}</div>

            @if ($room->gallery)
                <x-site.gallery :images="$room->gallery" :alt="$room->t('name')" :layout="$room->gallery_layout ?: 'mosaic'" />
            @endif

            @if ($highlights->isNotEmpty())
                <ul class="grid gap-4 {{ ['', 'sm:grid-cols-1', 'sm:grid-cols-2', 'sm:grid-cols-3'][min($highlights->count(), 3)] }}">
                    @foreach ($highlights as $h)
                        <li class="flex flex-col gap-4 rounded-3xl bg-forest-deep p-6 text-white">
                            <span class="text-ember-light">{{ site_icon($h['icon'], 'h-8 w-8') }}</span>
                            <span class="font-display text-[22px] leading-tight">{{ $h['text'] }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Бронь: закреплена при прокрутке на десктопе --}}
        <aside class="flex-1 lg:max-w-[400px]">
            <div class="sticky top-40 flex flex-col gap-6 rounded-3xl border border-mist bg-white p-7 md:p-8">
                @if ($amenities->isNotEmpty())
                    <div class="flex flex-col gap-6">
                        <h2 class="font-display text-2xl text-forest">{{ __('site.hotel.amenities') }}</h2>
                        @foreach ($groups as $key => $items)
                            <section class="flex flex-col gap-3">
                                <h3 class="text-[12px] uppercase tracking-[0.16em] text-stone">{{ __('site.hotel.amenity_groups.'.$key) }}</h3>
                                <ul class="flex flex-wrap gap-2">
                                    @foreach ($items as $a)
                                        <li class="inline-flex items-center gap-2 rounded-full bg-sand px-3.5 py-2 text-[14px] leading-none text-ink">
                                            <span class="shrink-0 text-ember">{{ site_icon($a['icon'], 'h-4 w-4') }}</span>{{ $a['text'] }}
                                        </li>
                                    @endforeach
                                </ul>
                            </section>
                        @endforeach
                    </div>
                @endif
                <div class="flex flex-col gap-5 {{ $amenities->isNotEmpty() ? 'border-t border-mist pt-6' : '' }}">
                    @if ($room->price_from)
                        <p class="flex flex-col gap-1 text-stone">
                            <span class="text-[12px] uppercase tracking-[0.16em]">{{ __('site.hotel.from') }}</span>
                            <span><strong class="font-display text-[40px] font-normal leading-none text-forest">{{ price($room->price_from) }} ₸</strong> <span class="text-[15px]">/ {{ __('site.hotel.per_night') }}</span></span>
                        </p>
                    @endif
                    <a href="{{ $room->bookingUrl() }}" class="btn-primary w-full">{{ __('site.hotel.book_room') }}</a>
                    <p class="text-center text-[13px] text-stone">{{ __('site.booking.best_rate') }}</p>
                </div>
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

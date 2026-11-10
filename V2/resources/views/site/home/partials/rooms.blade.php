{{-- Номера: первый номер крупно (как в макете) --}}
@if ($hotel && $rooms->isNotEmpty())
    <section class="band band-white bg-white">
        <div class="container-site flex flex-col gap-8 md:gap-12 py-14 md:py-20 lg:py-24">
            <x-site.section-head :eyebrow="__('site.hotel.rooms')" :title="__('site.home.rooms_title')" :link="$rooms->count() > 1 ? $hotel->sectionUrl('rooms') : null" :link-label="__('site.hotel.all_rooms')" />
            @php $room = $rooms->first(); @endphp
            <div class="flex flex-col overflow-hidden rounded-3xl bg-snow lg:flex-row">
                <a href="{{ $room->url() }}" class="aspect-[4/3] lg:aspect-auto lg:min-h-[520px] lg:flex-[1.4]" tabindex="-1" aria-hidden="true">
                    <x-site.media :src="$room->image" :alt="$room->t('name')" round="" />
                </a>
                <div class="flex flex-1 flex-col justify-center gap-6 p-8 lg:p-14">
                    <h3 class="font-display text-3xl leading-tight"><a href="{{ $room->url() }}" class="hover:text-ember">{{ $room->t('name') }}</a></h3>
                    <p class="lead">{{ $room->t('summary') }}</p>
                    @php
                        $facts = collect([
                            ['size', $room->size_m2 ? $room->size_m2.' м²' : null],
                            ['guests', $room->max_guests ? __('site.hotel.up_to_guests', ['count' => $room->max_guests]) : null],
                            ['bed', $room->t('bed')],
                            ['view', $room->t('view')],
                        ])->filter(fn ($f) => $f[1]);
                    @endphp
                    @if ($facts->isNotEmpty())
                        <ul class="flex flex-wrap gap-x-7 gap-y-3 border-t border-mist pt-6 text-base text-ink">
                            @foreach ($facts as [$icon, $text])
                                <li class="flex items-center gap-2.5"><span class="text-ember">{{ site_icon($icon, 'h-5 w-5') }}</span>{{ $text }}</li>
                            @endforeach
                        </ul>
                    @endif
                    @if ($amenities = array_slice($room->amenityList(), 0, 6))
                        <ul class="grid grid-cols-2 gap-x-6 gap-y-3 text-base text-slate">
                            @foreach ($amenities as $a)
                                <li class="flex items-center gap-2.5"><span class="text-forest">{{ amenity_icon($a, 'h-5 w-5') }}</span>{{ $a }}</li>
                            @endforeach
                        </ul>
                    @endif
                    <div class="flex flex-wrap items-center gap-7 pt-2">
                        @if ($room->price_from)
                            <span class="text-base text-slate">{{ __('site.hotel.from') }} <strong class="text-2xl font-normal text-ink">{{ price($room->price_from) }} ₸</strong> / {{ __('site.hotel.per_night') }}</span>
                        @endif
                        @if ($rooms->count() === 1)
                            <a href="{{ $room->url() }}" class="link-line">{{ __('site.common.learn_more') }}</a>
                        @else
                            <a href="{{ $room->bookingUrl() }}" class="btn-dark">{{ __('site.hotel.check_prices') }}</a>
                        @endif
                    </div>
                </div>
            </div>
            @if ($rooms->count() > 1)
                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($rooms->skip(1)->take(3) as $r)
                        @include('site.destinations.partials.room-card', ['room' => $r])
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endif

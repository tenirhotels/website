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
                    @php $tiles = $room->tiles(); @endphp
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
                    @if ($amenities = array_slice($room->listFeatures(), 0, 6))
                        <ul class="grid grid-cols-2 gap-x-5 gap-y-4 text-base leading-snug text-slate">
                            @foreach ($amenities as $a)
                                <li class="flex items-start gap-3"><span class="flex h-6 w-6 shrink-0 items-center justify-center text-forest">{{ site_icon($a['icon'], 'h-5 w-5') }}</span><span>{{ $a['text'] }}</span></li>
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

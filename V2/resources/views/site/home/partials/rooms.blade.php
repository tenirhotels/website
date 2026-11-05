{{-- Номера: первый номер крупно (как в макете) --}}
@if ($hotel && $rooms->isNotEmpty())
    <section class="bg-white">
        <div class="container-site flex flex-col gap-12 py-24">
            <x-site.section-head :eyebrow="__('site.hotel.rooms')" :title="__('site.home.rooms_title')" :link="$hotel->sectionUrl('rooms')" :link-label="__('site.hotel.all_rooms')" />
            @php($room = $rooms->first())
            <div class="flex flex-col bg-snow lg:flex-row">
                <a href="{{ $room->url() }}" class="aspect-[4/3] lg:aspect-auto lg:min-h-[520px] lg:flex-[1.4]" tabindex="-1" aria-hidden="true">
                    <x-site.media :src="$room->image" :alt="$room->t('name')" />
                </a>
                <div class="flex flex-1 flex-col justify-center gap-6 p-8 lg:p-14">
                    <h3 class="font-display text-4xl leading-tight"><a href="{{ $room->url() }}" class="hover:text-ember">{{ $room->t('name') }}</a></h3>
                    <p class="lead">{{ $room->t('summary') }}</p>
                    <ul class="flex flex-wrap gap-x-6 gap-y-2 text-sm text-slate">
                        @foreach (array_slice($room->amenityList(), 0, 4) as $a)<li>{{ $a }}</li>@endforeach
                    </ul>
                    <div class="flex flex-wrap items-center gap-7 pt-2">
                        @if ($room->price_from)
                            <span class="text-[15px] text-slate">{{ __('site.hotel.from') }} <strong class="text-2xl font-normal text-ink">{{ price($room->price_from) }} ₸</strong> / {{ __('site.hotel.per_night') }}</span>
                        @endif
                        <a href="{{ $room->bookingUrl() }}" class="btn-dark">{{ __('site.hotel.check_prices') }}</a>
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

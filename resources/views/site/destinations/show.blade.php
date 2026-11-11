<x-layouts.site :seo="$seo" :hotel="$hotel">
    <x-site.page-hero tall :image="$hotel->seasonHero()" :video="media($hotel->hero_video)"
        :eyebrow="collect([$hotel->altitude_m ? __('site.hotel.altitude', ['value' => number_format($hotel->altitude_m, 0, ',', ' ')]) : null, $hotel->t('region')])->filter()->implode(' · ')"
        :title="$hotel->t('name')" :text="$hotel->t('tagline')">
        @unless ($hotel->isOpen())
            <p class="eyebrow-light">{{ $hotel->t('opening_label') ?? __('site.hotel.coming_soon') }}</p>
        @endunless
    </x-site.page-hero>

    @if ($hotel->isOpen())
        <div class="container-site relative z-10 mt-8 -mb-6 md:-mb-12 lg:mb-0 lg:-mt-14">
            <x-site.booking-bar :hotel="$hotel" id="hotel" />
        </div>
    @endif

    <x-site.hotel-nav :hotel="$hotel" />

    <section class="container-site flex flex-col gap-8 md:gap-12 py-12 md:py-16 lg:py-24 lg:flex-row lg:gap-20">
        <div class="flex flex-[1.3] flex-col gap-6">
            <h2 class="h-section">{{ $hotel->t('summary') }}</h2>
            <div class="prose-tenir">{!! $hotel->t('description') !!}</div>
        </div>
        <aside class="flex flex-1 flex-col gap-8">
            @if ($hotel->highlights)
                <dl class="grid grid-cols-2 gap-6 border-y border-mist py-8">
                    @foreach ($hotel->highlights as $hl)
                        <div class="flex flex-col-reverse gap-1">
                            <dt class="text-xs uppercase tracking-[0.12em] text-stone">{{ \App\Support\Locales::pick($hl['label'] ?? []) }}</dt>
                            <dd class="font-display text-3xl text-forest">{{ $hl['value'] }}</dd>
                        </div>
                    @endforeach
                </dl>
            @endif
            <div class="flex flex-col gap-2 text-base text-slate">
                <p class="field-label mb-1">{{ __('site.hotel.contacts') }}</p>
                @if ($hotel->t('address'))<p>{{ $hotel->t('address') }}</p>@endif
                @if ($hotel->phone)<a href="tel:{{ preg_replace('/[^\d+]/', '', $hotel->phone) }}" class="hover:text-ember">{{ $hotel->phone }}</a>@endif
                @if ($hotel->email)<a href="mailto:{{ $hotel->email }}" class="hover:text-ember">{{ $hotel->email }}</a>@endif
                @if ($hotel->check_in_time)<p>{{ __('site.hotel.check_in_time') }} {{ $hotel->check_in_time }} · {{ __('site.hotel.check_out_time') }} {{ $hotel->check_out_time }}</p>@endif
                <a href="{{ $hotel->sectionUrl('getting-here') }}" class="link-line mt-3 self-start">{{ __('site.hotel.getting_here') }}</a>
            </div>
        </aside>
    </section>

    @if ($rooms->isNotEmpty())
        <section class="bg-white">
            <div class="container-site flex flex-col gap-8 md:gap-12 py-14 md:py-20 lg:py-24">
                <x-site.section-head :eyebrow="__('site.hotel.rooms')" :title="__('site.home.rooms_title')" :link="$hotel->sectionUrl('rooms')" :link-label="__('site.hotel.all_rooms')" />
                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($rooms as $room)
                        @include('site.destinations.partials.room-card', ['room' => $room])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($venues->isNotEmpty())
        @php($venue = $venues->first())
        <section class="container-site flex flex-col gap-8 md:gap-12 py-14 md:py-20 lg:py-24 lg:flex-row-reverse lg:items-center lg:gap-20">
            <div class="flex flex-1 flex-col gap-6">
                <p class="eyebrow">{{ __('site.hotel.dining') }}</p>
                <h2 class="h-section">{{ $venue->t('name') }}</h2>
                <p class="lead">{{ $venue->t('summary') }}</p>
                @if ($venue->t('hours'))<p class="text-[15px] text-stone">{{ $venue->t('hours') }}</p>@endif
                <a href="{{ $hotel->sectionUrl('dining') }}" class="link-line self-start">{{ __('site.common.learn_more') }}</a>
            </div>
            <div class="aspect-[4/3] w-full flex-1"><x-site.media :src="$venue->image" :alt="$venue->t('name')" /></div>
        </section>
    @endif

    @if ($experiences->isNotEmpty())
        <section class="bg-sand">
            <div class="container-site flex flex-col gap-8 md:gap-12 py-14 md:py-20 lg:py-24">
                <x-site.section-head :eyebrow="__('site.hotel.experiences')" :title="__('site.home.experiences_title')" :link="$hotel->sectionUrl('experiences')" />
                <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($experiences->take(3) as $e)
                        <x-site.card :href="$e->url()" :image="$e->image" :title="$e->t('title')" :tags="collect([$e->categoryLabel(), $e->seasonLabels()])->filter()->values()->all()" :text="$e->t('summary')" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($offers->isNotEmpty())
        <section class="container-site flex flex-col gap-8 md:gap-12 py-14 md:py-20 lg:py-24">
            <x-site.section-head :eyebrow="__('site.hotel.offers')" :title="__('site.home.offers_title')" :link="$hotel->sectionUrl('offers')" />
            <div class="grid gap-8 md:grid-cols-3">
                @foreach ($offers as $o)
                    <x-site.card :href="$o->url()" :image="$o->image" :title="$o->t('title')" :text="$o->t('summary')" ratio="aspect-[3/2]" :cta="__('site.common.book_now')" :cta-href="$o->bookingUrl()" />
                @endforeach
            </div>
        </section>
    @endif

</x-layouts.site>

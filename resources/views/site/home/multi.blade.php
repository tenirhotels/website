{{-- Главная в режиме сети: витрина бренда (как Six Senses / Aman). --}}
@php use App\Models\Setting; @endphp
<x-layouts.site :seo="$seo">

    <x-site.page-hero tall :image="$hotels->first()?->hero_image"
        :eyebrow="Setting::text('brand.name', 'Tenir Hotels')"
        :title="Setting::text('brand.tagline') ?? 'Tenir Hotels'"
        :text="__('site.destinations.intro')" />

    {{-- Плашка бронирования с выбором отеля --}}
    <div class="container-site relative z-10 mt-8 -mb-6 md:-mb-12 lg:mb-0 lg:-mt-14">
        <x-site.booking-bar id="home" />
    </div>

    {{-- Отели и курорты --}}
    <section class="band band-snow container-site flex flex-col gap-8 md:gap-12 py-14 md:py-20 lg:py-24">
        <x-site.section-head :eyebrow="__('site.nav.destinations')" :title="__('site.home.hotels_title')" :link="route('destinations.index')" />
        <div @class(['grid gap-x-8 gap-y-16', 'md:grid-cols-2' => $hotels->count() <= 2, 'md:grid-cols-2 lg:grid-cols-3' => $hotels->count() > 2])>
            @foreach ($hotels as $h)
                <x-site.card :href="$h->url()" :image="$h->hero_image" :title="$h->t('name')" ratio="aspect-[4/3]"
                    :meta="collect([$h->t('region'), $h->altitude_m ? number_format($h->altitude_m, 0, ',', ' ').' м' : null])->filter()->implode(' · ')"
                    :text="$h->t('summary')" :cta="__('site.hotel.discover')" />
            @endforeach
        </div>
    </section>

    @include('site.home.partials.experiences')
    @include('site.home.partials.offers')
    @include('site.home.partials.events')
    @if (\App\Models\Setting::get('gifts.on_home', false) === true)<x-site.gifts />@endif
    <x-site.recognition />
    @include('site.home.partials.stories-horizon')
</x-layouts.site>

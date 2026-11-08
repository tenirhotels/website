<x-layouts.site :seo="$seo" :hotel="$hotel">
    <x-site.page-hero :image="$hotel->gallery[0] ?? $hotel->hero_image" :eyebrow="$hotel->t('name')" :title="__('site.hotel.getting_here')" :text="$hotel->t('address')" />
    <x-site.hotel-nav :hotel="$hotel" />

    @if ($hotel->slug === config('tenir.recognition_hotel'))
        <x-site.access :hotel="$hotel" />
    @endif

    <section class="container-site flex flex-col gap-8 md:gap-14 pb-12 pt-0 md:py-16 lg:py-20 lg:flex-row lg:gap-20">
        <div class="prose-tenir flex-[1.5] [&>*:first-child]:mt-0">{!! $hotel->t('getting_here') !!}</div>
        <aside class="flex flex-1 flex-col gap-3 text-base text-slate">
            <a href="{{ route('map') }}" class="btn-primary self-start">{{ __('map.open_map') }}</a>
            @if ($hotel->latitude)
                <a href="https://www.google.com/maps/search/?api=1&query={{ $hotel->latitude }},{{ $hotel->longitude }}" class="btn-dark self-start" target="_blank" rel="noopener">Google Maps</a>
                <a href="https://2gis.kz/geo/{{ $hotel->longitude }},{{ $hotel->latitude }}" class="link-line mt-2 self-start" target="_blank" rel="noopener">2GIS</a>
            @endif
            @if ($hotel->phone)<a href="tel:{{ preg_replace('/[^\d+]/', '', $hotel->phone) }}" class="mt-6 hover:text-ember">{{ $hotel->phone }}</a>@endif
            @if ($hotel->whatsapp)<a href="https://wa.me/{{ preg_replace('/\D/', '', $hotel->whatsapp) }}" class="hover:text-ember">WhatsApp</a>@endif
        </aside>
    </section>
</x-layouts.site>

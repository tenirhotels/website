<x-layouts.site :seo="$seo" :hotel="$hotel">
    <x-site.page-hero :image="$hotel->hero_image" :eyebrow="$hotel->t('name')" :title="__('site.hotel.gallery')" />
    <x-site.hotel-nav :hotel="$hotel" />

    <section class="container-site py-12 md:py-16 lg:py-20">
        @if ($hotel->gallery)
            <x-site.gallery :images="$hotel->gallery" :alt="$hotel->t('name')" :layout="$hotel->gallery_layout ?: 'masonry'" />
        @else
            <p class="text-slate">{{ __('site.common.empty') }}</p>
        @endif
    </section>
</x-layouts.site>

<x-layouts.site :seo="$seo" :hotel="$hotel">
    <x-site.page-hero :image="$hotel->seasonHero()" :eyebrow="$hotel->t('name')" :title="__('site.reviews.title')" />
    <x-site.hotel-nav :hotel="$hotel" />
    <x-site.reviews :hotel="$hotel" :heading="false" />
</x-layouts.site>

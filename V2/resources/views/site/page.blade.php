<x-layouts.site :seo="$seo">
    <x-site.page-hero :image="$page->hero_image" :title="$page->t('title')" :text="$page->t('summary')" />
    <x-site.blocks :blocks="$page->blocks()" />
</x-layouts.site>

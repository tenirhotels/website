<x-layouts.site :seo="$seo" :hotel="$hotel">
    <x-site.page-hero :image="$experiences->first()?->image" :eyebrow="$hotel->t('name')" :title="__('site.hotel.experiences')" :text="__('site.experiences.intro')" />
    <x-site.hotel-nav :hotel="$hotel" />

    <section class="container-site py-12 md:py-16 lg:py-20">
        <div class="grid gap-x-8 gap-y-16 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($experiences as $e)
                <x-site.card :href="$e->url()" :image="$e->image" :title="$e->t('title')" :meta="collect([$e->categoryLabel(), $e->seasonLabels()])->filter()->implode(' · ')" :text="$e->t('summary')" />
            @empty
                <p class="text-slate">{{ __('site.common.empty') }}</p>
            @endforelse
        </div>
    </section>
</x-layouts.site>

<x-layouts.site :seo="$seo">
    <x-site.page-hero :image="$open->first()?->hero_image" :title="__('site.nav.destinations')" :text="__('site.destinations.intro')" />

    <section class="container-site flex flex-col gap-8 md:gap-12 py-12 md:py-16 lg:py-20">
        <div class="grid gap-x-8 gap-y-16 md:grid-cols-2">
            @foreach ($open as $h)
                <x-site.card :href="$h->url()" :image="$h->hero_image" :title="$h->t('name')" ratio="aspect-[3/2]"
                    :meta="collect([$h->t('region'), $h->altitude_m ? number_format($h->altitude_m, 0, ',', ' ').' м' : null])->filter()->implode(' · ')"
                    :text="$h->t('summary')" :cta="__('site.hotel.discover')" />
            @endforeach
        </div>
    </section>

    @if ($comingSoon->isNotEmpty())
        <section class="bg-sand">
            <div class="container-site flex flex-col gap-8 md:gap-12 py-12 md:py-16 lg:py-20">
                <x-site.section-head :title="__('site.destinations.coming_soon')" />
                <div class="grid gap-8 md:grid-cols-3">
                    @foreach ($comingSoon as $h)
                        <x-site.card :href="$h->url()" :image="$h->hero_image" :title="$h->t('name')" :meta="$h->t('opening_label') ?? __('site.hotel.coming_soon')" :text="$h->t('summary')" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-layouts.site>

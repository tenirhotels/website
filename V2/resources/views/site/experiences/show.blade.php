<x-layouts.site :seo="$seo" :hotel="$experience->hotel">
    <x-site.page-hero :image="$experience->image" :eyebrow="collect([$experience->categoryLabel(), $experience->hotel?->t('name')])->filter()->implode(' · ')" :title="$experience->t('title')" :text="$experience->t('summary')" />

    <section class="container-site flex flex-col gap-8 md:gap-14 py-12 md:py-16 lg:py-20 lg:flex-row lg:gap-20">
        <div class="flex flex-[1.5] flex-col gap-10">
            <div class="prose-tenir">{!! $experience->t('description') !!}</div>
            @if ($experience->gallery)
                <div class="grid grid-cols-2 gap-3">
                    @foreach ($experience->gallery as $img)
                        <div class="aspect-[4/3] overflow-hidden rounded-2xl"><x-site.media :src="$img" :alt="$experience->t('title')" /></div>
                    @endforeach
                </div>
            @endif
        </div>
        <aside class="flex-1">
            <dl class="flex flex-col gap-5 bg-white p-8 rounded-3xl text-[15px]">
                @if ($experience->seasonLabels())
                    <div><dt class="field-label mb-1">{{ __('site.common.seasons') }}</dt><dd>{{ $experience->seasonLabels() }}</dd></div>
                @endif
                @if ($experience->t('duration'))
                    <div><dt class="field-label mb-1">{{ __('site.common.duration') }}</dt><dd>{{ $experience->t('duration') }}</dd></div>
                @endif
                @if ($experience->price_from)
                    <div><dt class="sr-only">{{ __('site.hotel.from') }}</dt><dd class="text-2xl">{{ __('site.common.price_from', ['price' => price($experience->price_from)]) }}</dd></div>
                @endif
                <x-site.cta-box :cta="$experience->cta" :title="$experience->t('title')" :hotel="$experience->hotel" />
            </dl>
        </aside>
    </section>

    @if ($related->isNotEmpty())
        <section class="bg-sand">
            <div class="container-site flex flex-col gap-8 md:gap-12 py-12 md:py-16 lg:py-20">
                <x-site.section-head :title="__('site.common.related')" />
                <div class="grid gap-8 md:grid-cols-3">
                    @foreach ($related as $e)
                        <x-site.card :href="$e->url()" :image="$e->image" :title="$e->t('title')" :text="$e->t('summary')" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-layouts.site>

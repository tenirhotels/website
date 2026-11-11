<x-layouts.site :seo="$seo" :hotel="$experience->hotel">
    <x-site.page-hero :image="$experience->image" :eyebrow="collect([$experience->categoryLabel(), $experience->hotel?->t('name')])->filter()->implode(' · ')" :title="$experience->t('title')" :text="$experience->t('summary')" />

    <section class="container-site flex flex-col gap-8 md:gap-14 py-12 md:py-16 lg:py-20 lg:flex-row lg:gap-20">
        <div class="flex flex-[1.5] flex-col gap-10">
            <div class="prose-tenir">{!! $experience->t('description') !!}</div>
            @if ($experience->gallery)
                <x-site.gallery :images="$experience->gallery" :alt="$experience->t('title')" :layout="$experience->gallery_layout ?: 'mosaic'" />
            @endif
        </div>
        <aside class="flex-1">
            <dl class="flex flex-col gap-5 bg-white p-8 rounded-3xl text-base">
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
            <div class="container-site flex flex-col gap-6 py-10 md:py-12">
                <div class="flex flex-wrap items-baseline justify-between gap-3">
                    <h2 class="font-display text-2xl leading-tight">{{ __('site.common.related') }}</h2>
                    <a href="{{ $experience->hotel?->sectionUrl('experiences') ?? route('experiences.index') }}" class="link-line">{{ __('site.common.all_experiences') }}</a>
                </div>
                <div class="grid grid-cols-2 gap-x-4 gap-y-6 md:grid-cols-3 lg:grid-cols-6">
                    @foreach ($related as $e)
                        <a href="{{ $e->url() }}" class="group relative block aspect-[5/6] overflow-hidden rounded-2xl">
                            <x-site.media :src="$e->image" :alt="''" class="transition-transform duration-700 group-hover:scale-[1.04]" />
                            <span class="pointer-events-none absolute inset-0 bg-[linear-gradient(to_top,rgb(22_33_27/.9)_0%,rgb(22_33_27/.45)_30%,rgb(22_33_27/0)_60%)]" aria-hidden="true"></span>
                            <span class="absolute inset-x-3.5 bottom-3.5 line-clamp-2 font-display text-[17px] leading-[1.2] text-white">{{ $e->t('title') }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-layouts.site>

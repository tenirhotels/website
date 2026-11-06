@if ($experiences->isNotEmpty())
    <section class="container-site flex flex-col gap-8 md:gap-12 py-14 md:py-20 lg:py-24">
        <x-site.section-head :eyebrow="__('site.nav.experiences')" :title="__('site.home.experiences_title')" :link="route('experiences.index')" />
        <div class="rail sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($experiences as $e)
                <x-site.card :href="$e->url()" :image="$e->image" :title="$e->t('title')"
                    :meta="collect([\App\Support\Property::isMulti() ? $e->hotel?->t('name') : null, $e->seasonLabels() ?: $e->categoryLabel()])->filter()->implode(' · ')"
                    :text="$e->t('summary')" />
            @endforeach
        </div>
    </section>
@endif

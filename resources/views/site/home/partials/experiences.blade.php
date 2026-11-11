@if ($experiences->isNotEmpty())
    <section class="band band-snow container-site flex flex-col gap-8 md:gap-12 py-14 md:py-20 lg:py-24">
        <x-site.section-head :eyebrow="__('site.nav.experiences')" :title="__('site.home.experiences_title')" :link="\App\Models\Experience::published()->count() > $experiences->count() ? route('experiences.index') : null" />
        <div class="rail sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($experiences as $e)
                <x-site.card :href="$e->url()" :image="$e->image" :title="$e->t('title')"
                    :tags="collect([\App\Support\Property::isMulti() ? $e->hotel?->t('name') : null, $e->categoryLabel(), $e->seasonLabels()])->filter()->values()->all()"
                    :text="$e->t('summary')" />
            @endforeach
        </div>
    </section>
@endif

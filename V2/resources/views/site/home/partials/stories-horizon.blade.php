{{-- Истории --}}
@if ($stories->isNotEmpty())
<section class="band band-white bg-white">
    <div class="container-site flex flex-col gap-8 md:gap-12 py-14 md:py-20 lg:py-24 lg:flex-row lg:gap-16">
        <div class="flex flex-1 flex-col gap-10">
                <x-site.section-head :title="__('site.home.stories_title')" :link="\App\Models\Story::live()->count() > $stories->count() ? route('stories.index') : null" />
                <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($stories->take(3) as $s)
                        <x-site.card :href="$s->url()" :image="$s->image" :title="$s->t('title')" :meta="$s->published_at?->translatedFormat('j F Y')" ratio="aspect-[3/2]" />
                    @endforeach
                </div>
        </div>
    </div>
</section>
@endif

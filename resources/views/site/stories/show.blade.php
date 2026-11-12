<x-layouts.site :seo="$seo" :hotel="$story->hotel">
    <x-site.page-hero :image="$story->image" :eyebrow="$story->published_at?->translatedFormat('j F Y')" :title="$story->t('title')" :text="$story->t('summary')" />

    <article class="container-site max-w-3xl py-12 md:py-16 lg:py-20">
        <div class="prose-tenir">{!! $story->t('body') !!}</div>
        <a href="{{ route('stories.index') }}" class="link-line mt-12">{{ __('site.common.back') }}</a>
    </article>

    @if ($more->isNotEmpty())
        <section class="bg-white">
            <div class="container-site flex flex-col gap-8 md:gap-12 py-12 md:py-16 lg:py-20">
                <x-site.section-head :title="__('site.nav.stories')" :link="route('stories.index')" />
                <div class="grid gap-8 md:grid-cols-3">
                    @foreach ($more as $s)
                        <x-site.card :href="$s->url()" :image="$s->image" :title="$s->t('title')" ratio="aspect-[3/2]" :meta="$s->published_at?->translatedFormat('j F Y')" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-layouts.site>

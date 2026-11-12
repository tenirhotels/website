<x-layouts.site :seo="$seo">
    <x-site.page-hero :image="$stories->first()?->image" :title="__('site.nav.stories')" />

    <section class="container-site py-12 md:py-16 lg:py-20">
        <div class="grid gap-x-8 gap-y-16 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($stories as $s)
                <x-site.card :href="$s->url()" :image="$s->image" :title="$s->t('title')" ratio="aspect-[3/2]"
                    :meta="$s->published_at?->translatedFormat('j F Y')" :text="$s->t('summary')" />
            @empty
                <p class="text-slate">{{ __('site.common.empty') }}</p>
            @endforelse
        </div>
        <div class="mt-16">{{ $stories->links() }}</div>
    </section>
</x-layouts.site>

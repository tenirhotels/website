<x-layouts.site :seo="$seo" :hotel="$hotel">
    <x-site.page-hero :image="$offers->first()?->image" :eyebrow="$hotel->t('name')" :title="__('site.hotel.offers')" :text="__('site.offers.intro')" />
    <x-site.hotel-nav :hotel="$hotel" />

    <section class="container-site py-20">
        <div class="grid gap-x-8 gap-y-16 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($offers as $o)
                @include('site.offers.partials.card', ['offer' => $o])
            @empty
                <p class="text-slate">{{ __('site.common.empty') }}</p>
            @endforelse
        </div>
    </section>
</x-layouts.site>

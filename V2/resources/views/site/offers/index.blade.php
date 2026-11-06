<x-layouts.site :seo="$seo">
    <x-site.page-hero :image="$offers->first()?->image" :title="__('site.nav.offers')" :text="__('site.offers.intro')" />

    <section class="bg-sand">
        <div class="container-site flex flex-col gap-8 md:gap-12 py-12 md:py-16 lg:py-20">
            @include('site.partials.hotel-filter', ['route' => 'offers.index'])
            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($offers as $o)
                    @include('site.offers.partials.card', ['offer' => $o])
                @empty
                    <p class="text-slate">{{ __('site.common.empty') }}</p>
                @endforelse
            </div>
        </div>
    </section>
</x-layouts.site>

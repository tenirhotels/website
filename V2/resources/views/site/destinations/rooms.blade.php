<x-layouts.site :seo="$seo" :hotel="$hotel">
    <x-site.page-hero :image="$hotel->gallery[1] ?? $hotel->hero_image" :eyebrow="$hotel->t('name')" :title="__('site.hotel.rooms')" :text="__('site.home.rooms_title')" />
    <x-site.hotel-nav :hotel="$hotel" />

    <section class="container-site py-12 md:py-16 lg:py-20">
        <div class="grid gap-x-8 gap-y-16 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($rooms as $room)
                @include('site.destinations.partials.room-card', ['room' => $room])
            @empty
                <p class="text-slate">{{ __('site.common.empty') }}</p>
            @endforelse
        </div>
    </section>
</x-layouts.site>

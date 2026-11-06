<x-layouts.site :seo="$seo" :hotel="$hotel">
    <x-site.page-hero :image="$hotel->hero_image" :eyebrow="$hotel->t('name')" :title="__('site.hotel.gallery')" />
    <x-site.hotel-nav :hotel="$hotel" />

    <section class="container-site py-20">
        @if ($hotel->gallery)
            <div class="columns-1 gap-4 sm:columns-2 lg:columns-3">
                @foreach ($hotel->gallery as $img)
                    <a href="{{ media($img) }}" class="mb-4 block break-inside-avoid overflow-hidden rounded-2xl" target="_blank" rel="noopener">
                        <x-site.media :src="$img" :alt="$hotel->t('name')" class="h-auto w-full" />
                    </a>
                @endforeach
            </div>
        @else
            <p class="text-slate">{{ __('site.common.empty') }}</p>
        @endif
    </section>
</x-layouts.site>

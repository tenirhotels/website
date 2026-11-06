<x-layouts.site :seo="$seo" :hotel="$hotel">
    <x-site.page-hero :image="$hotel->gallery[0] ?? $hotel->hero_image" :eyebrow="$hotel->t('name')" :title="__('site.hotel.getting_here')" :text="$hotel->t('address')" />
    <x-site.hotel-nav :hotel="$hotel" />

    <section class="container-site flex flex-col gap-14 py-20 lg:flex-row lg:gap-20">
        <div class="prose-tenir flex-[1.5]">{!! $hotel->t('getting_here') !!}</div>
        <aside class="flex flex-1 flex-col gap-3 text-[15px] text-slate">
            @if ($hotel->latitude)
                <a href="https://www.google.com/maps/search/?api=1&query={{ $hotel->latitude }},{{ $hotel->longitude }}" class="btn-dark self-start" target="_blank" rel="noopener">Google Maps</a>
                <a href="https://2gis.kz/geo/{{ $hotel->longitude }},{{ $hotel->latitude }}" class="link-line mt-2 self-start" target="_blank" rel="noopener">2GIS</a>
            @endif
            @if ($hotel->phone)<a href="tel:{{ preg_replace('/[^\d+]/', '', $hotel->phone) }}" class="mt-6 hover:text-ember">{{ $hotel->phone }}</a>@endif
            @if ($hotel->whatsapp)<a href="https://wa.me/{{ preg_replace('/\D/', '', $hotel->whatsapp) }}" class="hover:text-ember">WhatsApp</a>@endif
        </aside>
    </section>

    @if ($faqs->isNotEmpty())
        <section class="bg-white">
            <div class="container-site max-w-4xl py-20">
                <h2 class="h-section mb-10">{{ __('site.hotel.faq') }}</h2>
                <div class="divide-y divide-mist border-y border-mist">
                    @foreach ($faqs as $faq)
                        <details class="group py-5">
                            <summary class="flex cursor-pointer list-none items-center justify-between gap-6 text-lg">
                                {{ $faq->t('question') }}
                                <span class="text-2xl text-ember transition-transform group-open:rotate-45" aria-hidden="true">+</span>
                            </summary>
                            <div class="prose-tenir mt-4">{!! $faq->t('answer') !!}</div>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-layouts.site>

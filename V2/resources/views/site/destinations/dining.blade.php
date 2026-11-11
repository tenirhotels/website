<x-layouts.site :seo="$seo" :hotel="$hotel">
    <x-site.page-hero :image="$venues->first()?->image" :eyebrow="$hotel->t('name')" :title="__('site.hotel.dining')" :text="$venues->first()?->t('summary')" />
    <x-site.hotel-nav :hotel="$hotel" />

    @forelse ($venues as $i => $venue)
        <section id="venue-{{ $venue->id }}" class="container-site scroll-mt-24 py-12 md:py-16 lg:py-20">
            <div @class(['flex flex-col gap-8 md:gap-12 lg:flex-row lg:items-start lg:gap-20', 'lg:flex-row-reverse' => $i % 2])>
                <div class="flex flex-1 flex-col gap-6">
                    <h2 class="h-section">{{ $venue->t('name') }}</h2>
                    @if ($venue->t('hours') || $venue->phone || $venue->phone_ext || $venue->email)
                        <dl class="grid grid-cols-1 gap-x-8 gap-y-5 rounded-2xl border border-mist bg-white p-6 sm:grid-cols-2">
                            @if ($venue->t('hours'))
                                <div><dt class="field-label mb-1.5">{{ __('site.hotel.hours') }}</dt><dd class="text-[17px] leading-snug text-ink">{{ $venue->t('hours') }}</dd></div>
                            @endif
                            @if ($venue->phone)
                                <div><dt class="field-label mb-1.5">{{ __('site.hotel.venue_phone') }}</dt><dd class="text-[17px] leading-snug text-ink"><a href="tel:{{ preg_replace('/[^\d+]/', '', $venue->phone) }}" class="hover:text-ember">{{ $venue->phone }}</a></dd></div>
                            @endif
                            @if ($venue->phone_ext)
                                <div><dt class="field-label mb-1.5">{{ __('site.hotel.venue_ext') }}</dt><dd class="text-[17px] leading-snug text-ink">{{ $venue->phone_ext }}</dd></div>
                            @endif
                            @if ($venue->email)
                                <div><dt class="field-label mb-1.5">{{ __('site.hotel.venue_email') }}</dt><dd class="text-[17px] leading-snug text-ink"><a href="mailto:{{ $venue->email }}" class="hover:text-ember">{{ email_wrap($venue->email) }}</a></dd></div>
                            @endif
                        </dl>
                    @endif
                    <div class="prose-tenir !text-[17px] !leading-[1.7] [&_p:last-child]:mb-0">{!! $venue->t('description') !!}</div>
                    @if ($venue->menu_file)
                        <a href="{{ media($venue->menu_file) }}" class="btn-dark self-start" target="_blank" rel="noopener">{{ __('site.hotel.menu') }}</a>
                    @endif
                </div>
                <div class="flex-1">
                    <x-site.gallery :images="array_merge([$venue->image], $venue->gallery ?? [])" :alt="$venue->t('name')" :layout="$venue->gallery_layout ?: 'slider'" />
                </div>
            </div>
        </section>
    @empty
        <p class="container-site py-12 md:py-16 lg:py-20 text-slate">{{ __('site.common.empty') }}</p>
    @endforelse
</x-layouts.site>

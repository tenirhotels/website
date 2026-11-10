<x-layouts.site :seo="$seo" :hotel="$hotel">
    <x-site.page-hero :image="$venues->first()?->image" :eyebrow="$hotel->t('name')" :title="__('site.hotel.dining')" :text="$venues->first()?->t('summary')" />
    <x-site.hotel-nav :hotel="$hotel" />

    @forelse ($venues as $i => $venue)
        <section id="venue-{{ $venue->id }}" class="container-site scroll-mt-24 py-12 md:py-16 lg:py-20">
            <div @class(['flex flex-col gap-8 md:gap-12 lg:flex-row lg:items-start lg:gap-20', 'lg:flex-row-reverse' => $i % 2])>
                <div class="flex flex-1 flex-col gap-6">
                    <h2 class="h-section">{{ $venue->t('name') }}</h2>
                    @if ($venue->t('hours'))
                        <p><span class="field-label">{{ __('site.hotel.hours') }}</span> <span class="text-slate">{{ $venue->t('hours') }}</span></p>
                    @endif
                    @if ($venue->phone || $venue->phone_ext || $venue->email)
                        <ul class="flex flex-col gap-2 text-slate">
                            @if ($venue->phone)<li><span class="field-label">{{ __('site.hotel.venue_phone') }}</span> <a href="tel:{{ preg_replace('/[^\d+]/', '', $venue->phone) }}" class="hover:text-ember">{{ $venue->phone }}</a></li>@endif
                            @if ($venue->phone_ext)<li><span class="field-label">{{ __('site.hotel.venue_ext') }}</span> <span class="text-ink">{{ $venue->phone_ext }}</span></li>@endif
                            @if ($venue->email)<li><span class="field-label">{{ __('site.hotel.venue_email') }}</span> <a href="mailto:{{ $venue->email }}" class="hover:text-ember">{{ email_wrap($venue->email) }}</a></li>@endif
                        </ul>
                    @endif
                    <div class="prose-tenir">{!! $venue->t('description') !!}</div>
                    @if ($venue->menu_file)
                        <a href="{{ media($venue->menu_file) }}" class="link-line self-start" target="_blank" rel="noopener">{{ __('site.hotel.menu') }}</a>
                    @endif
                </div>
                <div class="flex flex-1 flex-col gap-3">
                    <div class="aspect-[4/3]"><x-site.media :src="$venue->image" :alt="$venue->t('name')" /></div>
                    @if ($venue->gallery)
                        <div class="grid grid-cols-3 gap-3">
                            @foreach (array_slice($venue->gallery, 0, 3) as $img)
                                <div class="aspect-square"><x-site.media :src="$img" alt="" /></div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @empty
        <p class="container-site py-12 md:py-16 lg:py-20 text-slate">{{ __('site.common.empty') }}</p>
    @endforelse
</x-layouts.site>

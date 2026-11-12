@php(request()->attributes->set('tenir.book_url', $expired ? null : $offer->bookingUrl()))
<x-layouts.site :seo="$seo" :hotel="$offer->hotel">
    <x-site.page-hero :image="$offer->image" :eyebrow="$offer->hotel?->t('name') ?? __('site.nav.offers')" :title="$offer->t('title')" />

    <section class="container-site flex flex-col gap-8 md:gap-14 py-12 md:py-16 lg:py-20 lg:flex-row lg:gap-20">
        <div class="flex flex-[1.5] flex-col gap-10">
            @if ($expired)
                <p class="border-l-0 rounded-2xl bg-sand p-5 text-slate">{{ __('site.offers.expired') }}</p>
            @endif
            @if ($offer->t('description'))
                <div class="prose-tenir">{!! $offer->t('description') !!}</div>
            @endif
            @if ($offer->t('included'))
                <div>
                    <h2 class="mb-5 font-display text-2xl">{{ __('site.common.included') }}</h2>
                    <div class="prose-tenir">{!! $offer->t('included') !!}</div>
                </div>
            @endif
            @if ($offer->t('terms'))
                <div>
                    <h2 class="mb-5 font-display text-2xl">{{ __('site.common.terms') }}</h2>
                    <div class="prose-tenir text-base">{!! $offer->t('terms') !!}</div>
                </div>
            @endif
        </div>
        @php($facts = array_filter([
                    __('site.offers.fact_stay') => $offer->min_nights ? __('site.offers.min_nights', ['count' => $offer->min_nights]) : null,
                    __('site.offers.fact_until') => $offer->valid_to?->translatedFormat('j F Y'),
        ]))
        <aside @class(['flex-1', 'max-lg:hidden' => ! $facts && ! $offer->promo_code])>
            <div class="sticky top-32 flex flex-col gap-6 rounded-3xl bg-white p-6 md:p-8">
                @if ($facts || $offer->promo_code)
                    <div class="flex flex-col gap-4">
                        <p class="eyebrow">{{ __('site.offers.facts') }}</p>
                        <dl class="flex flex-col divide-y divide-forest/10">
                            @foreach ($facts as $label => $value)
                                <div class="flex items-baseline justify-between gap-6 py-3 first:pt-0">
                                    <dt class="text-sm text-stone">{{ $label }}</dt>
                                    <dd class="text-right font-medium text-forest">{{ $value }}</dd>
                                </div>
                            @endforeach
                        </dl>
                        @if ($offer->promo_code)
                            <div class="flex items-center justify-between gap-4 rounded-2xl border border-dashed border-ember/50 bg-ember/5 px-5 py-4">
                                <span class="text-sm text-stone">{{ __('site.offers.fact_promo') }}</span>
                                <span class="font-mono text-lg font-semibold tracking-wider text-ember">{{ $offer->promo_code }}</span>
                            </div>
                        @endif
                    </div>
                @endif
                @unless ($expired)
                    <div class="flex flex-col gap-3 max-lg:hidden">
                        <a href="{{ $offer->bookingUrl() }}" class="btn-primary w-full">{{ __('site.common.book_now') }}</a>
                        <p class="text-center text-[13px] text-stone">{{ __('site.booking.best_rate') }}</p>
                    </div>
                @endunless
            </div>
        </aside>
    </section>

    @if ($others->isNotEmpty())
        <section class="bg-sand">
            <div class="container-site flex flex-col gap-8 md:gap-12 py-12 md:py-16 lg:py-20">
                <x-site.section-head :title="__('site.offers.others')" :link="route('offers.index')" />
                <div @class(['grid gap-5 sm:grid-cols-2', 'lg:grid-cols-3' => $others->count() > 2])>
                    @foreach ($others as $o)
                        @include('site.offers.partials.card', ['offer' => $o])
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-layouts.site>

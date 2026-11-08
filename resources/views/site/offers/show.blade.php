@php(request()->attributes->set('tenir.book_url', $expired ? null : $offer->bookingUrl()))
<x-layouts.site :seo="$seo" :hotel="$offer->hotel">
    <x-site.page-hero :image="$offer->image" :eyebrow="$offer->hotel?->t('name') ?? __('site.nav.offers')" :title="$offer->t('title')" :text="$offer->t('summary')" />

    <section class="container-site flex flex-col gap-8 md:gap-14 py-12 md:py-16 lg:py-20 lg:flex-row lg:gap-20">
        <div class="flex flex-[1.5] flex-col gap-10">
            @if ($expired)
                <p class="border-l-0 rounded-2xl bg-sand p-5 text-slate">{{ __('site.offers.expired') }}</p>
            @endif
            @if ($offer->t('description'))
                <div class="prose-tenir">{!! $offer->t('description') !!}</div>
            @endif
            @if ($offer->t('terms'))
                <div>
                    <h2 class="mb-5 font-display text-2xl">{{ __('site.common.terms') }}</h2>
                    <div class="prose-tenir text-base">{!! $offer->t('terms') !!}</div>
                </div>
            @endif
        </div>
        <aside class="flex-1">
            <div class="sticky top-32 flex flex-col gap-4 bg-white p-8 rounded-3xl">
                @if ($offer->min_nights)<p>{{ __('site.offers.min_nights', ['count' => $offer->min_nights]) }}</p>@endif
                @if ($offer->valid_to)<p class="text-slate">{{ __('site.common.valid_until', ['date' => $offer->valid_to->translatedFormat('j F Y')]) }}</p>@endif
                @if ($offer->promo_code)<p class="text-slate">{{ __('site.offers.promo', ['code' => $offer->promo_code]) }}</p>@endif
                @unless ($expired)
                    <a href="{{ $offer->bookingUrl() }}" class="btn-primary mt-2 w-full max-lg:hidden">{{ __('site.common.book_now') }}</a>
                    <p class="text-center text-[13px] text-stone">{{ __('site.booking.best_rate') }}</p>
                @endunless
            </div>
        </aside>
    </section>

    @if ($others->isNotEmpty())
        <section class="bg-sand">
            <div class="container-site flex flex-col gap-8 md:gap-12 py-12 md:py-16 lg:py-20">
                <x-site.section-head :title="__('site.common.related')" :link="route('offers.index')" />
                <div class="grid gap-8 md:grid-cols-3">
                    @foreach ($others as $o)
                        @include('site.offers.partials.card', ['offer' => $o])
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-layouts.site>

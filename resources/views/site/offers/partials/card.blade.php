<article class="group flex flex-col overflow-hidden rounded-3xl bg-white">
    <a href="{{ $offer->url() }}" class="block aspect-[3/2] overflow-hidden" tabindex="-1" aria-hidden="true">
        <x-site.media :src="$offer->image" :alt="$offer->t('title')" round="rounded-none" class="transition-transform duration-700 group-hover:scale-[1.03]" />
    </a>
    <div class="flex flex-1 flex-col gap-3 p-8">
        <p class="text-xs uppercase tracking-[0.2em] text-stone">
            {{ collect([
                \App\Support\Property::isMulti() ? ($offer->hotel?->t('name') ?? __('site.common.whole_network')) : null,
                $offer->min_nights ? __('site.offers.min_nights', ['count' => $offer->min_nights]) : null,
                $offer->valid_to ? __('site.common.valid_until', ['date' => $offer->valid_to->translatedFormat('j F Y')]) : null,
            ])->filter()->implode(' · ') }}
        </p>
        <h3 class="font-display text-[28px] leading-tight"><a href="{{ $offer->url() }}" class="hover:text-ember">{{ $offer->t('title') }}</a></h3>
        <p class="leading-relaxed text-slate">{{ $offer->t('summary') }}</p>
        <div class="mt-auto flex flex-wrap items-center gap-6 pt-3">
            <a href="{{ $offer->bookingUrl() }}" class="btn-primary">{{ __('site.common.book_now') }}</a>
            <a href="{{ $offer->url() }}" class="text-[13px] uppercase tracking-[0.2em] hover:text-ember">{{ __('site.common.learn_more') }}</a>
        </div>
    </div>
</article>

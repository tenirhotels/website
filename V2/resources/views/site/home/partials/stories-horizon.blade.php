{{-- Истории + «Скоро» (как On the horizon у Six Senses) + подписка --}}
@php($comingSoon ??= collect())
<section class="bg-white">
    <div class="container-site flex flex-col gap-8 md:gap-12 py-14 md:py-20 lg:py-24 lg:flex-row lg:gap-16">
        @if ($stories->isNotEmpty())
            <div class="flex flex-[1.4] flex-col gap-10">
                <x-site.section-head :title="__('site.home.stories_title')" :link="\App\Models\Story::live()->count() > $stories->count() ? route('stories.index') : null" />
                <div class="grid gap-8 sm:grid-cols-2">
                    @foreach ($stories->take(2) as $s)
                        <x-site.card :href="$s->url()" :image="$s->image" :title="$s->t('title')" :meta="$s->published_at?->translatedFormat('j F Y')" ratio="aspect-[3/2]" />
                    @endforeach
                </div>
            </div>
        @endif
        <div class="flex flex-1 flex-col justify-center gap-5 rounded-3xl bg-snow p-10 lg:p-12">
            <p class="eyebrow text-stone">{{ __('site.hotel.coming_soon') }}</p>
            <h2 class="font-display text-4xl leading-tight">{{ \App\Models\Setting::text('brand.newsletter_heading') ?? __('site.home.coming_title') }}</h2>
            @forelse ($comingSoon as $h)
                <a href="{{ $h->url() }}" class="flex items-baseline justify-between gap-4 border-t border-mist pt-4 hover:text-ember">
                    <span class="font-display text-2xl">{{ $h->t('name') }}</span>
                    <span class="text-sm text-stone">{{ $h->t('opening_label') }}</span>
                </a>
            @empty
                <p class="leading-relaxed text-slate">{{ __('site.home.coming_text') }}</p>
            @endforelse
            {{-- Подписка: подключить к Bitrix24 / сервису рассылок --}}
            <form class="mt-2 flex flex-wrap gap-2" action="#" method="post" onsubmit="return false">
                <label for="newsletter-email" class="sr-only">{{ __('site.common.email') }}</label>
                <input id="newsletter-email" type="email" required autocomplete="email" placeholder="{{ __('site.common.email') }}" class="min-h-12 flex-1 basis-56 rounded-full border border-[#C9C6BD] bg-white px-5 text-[15px]">
                <button type="submit" class="btn-dark">{{ __('site.common.subscribe') }}</button>
            </form>
        </div>
    </div>
</section>

@php
    use App\Models\Setting;
    // Блоки бренда и sustainability на главной берутся со страниц about и sustainability.
    $word = collect($brandPage?->blocks() ?? [])->firstWhere('type', 'word')['data'] ?? null;
    $pillars = collect($sustainabilityPage?->blocks() ?? [])->firstWhere('type', 'pillars')['data'] ?? null;
@endphp
<x-layouts.site :seo="$seo" :hotel="$featured">

    {{-- 1. Герой (как у Aman: один сильный кадр и одна фраза) --}}
    <x-site.page-hero tall :image="$featured?->hero_image"
        :eyebrow="$featured ? collect([$featured->altitude_m ? number_format($featured->altitude_m, 0, ',', ' ').' м' : null, $featured->t('name'), $featured->t('city')])->filter()->implode(' · ') : null"
        :title="Setting::text('brand.tagline') ?? $featured?->t('tagline') ?? 'Tenir Hotels'"
        :text="$featured?->t('summary')" />

    {{-- 2. Плашка бронирования поверх героя --}}
    <div class="container-site relative z-10 -mt-16 hidden max-w-[1280px] lg:block">
        <x-site.booking-bar :hotel="$featured" id="home" />
        <p class="mt-3 text-[13px] text-stone">{{ __('site.booking.best_rate') }}</p>
    </div>

    {{-- 3. Отель (пока один) или витрина отелей --}}
    @if ($hotels->count() > 1)
        <section class="container-site py-24 md:py-32">
            <x-site.section-head :title="__('site.home.hotels_title')" :link="route('destinations.index')" class="mb-12" />
            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($hotels as $h)
                    <x-site.card :href="$h->url()" :image="$h->hero_image" :title="$h->t('name')" :meta="$h->t('region')" :text="$h->t('summary')" />
                @endforeach
            </div>
        </section>
    @elseif ($featured)
        <section class="container-site flex flex-col gap-16 py-24 md:py-32 lg:flex-row lg:items-center lg:gap-20">
            <div class="flex flex-1 flex-col gap-7">
                <p class="eyebrow">{{ $featured->t('name') }}</p>
                <h2 class="h-section">{{ $featured->t('tagline') }}</h2>
                <p class="lead">{{ str(strip_tags((string) $featured->t('description')))->squish()->limit(420, preserveWords: true) }}</p>
                @if ($featured->highlights)
                    <dl class="flex flex-wrap gap-x-12 gap-y-6 border-t border-mist pt-6">
                        @foreach ($featured->highlights as $hl)
                            <div class="flex flex-col-reverse gap-1">
                                <dt class="text-[13px] uppercase tracking-[0.12em] text-stone">{{ \App\Support\Locales::pick($hl['label'] ?? []) }}</dt>
                                <dd class="font-display text-[44px] leading-none text-forest">{{ $hl['value'] }}</dd>
                            </div>
                        @endforeach
                    </dl>
                @endif
                <a href="{{ $featured->url() }}" class="link-line self-start">{{ __('site.hotel.discover') }}</a>
            </div>
            <div class="aspect-[5/6] w-full flex-1 lg:max-w-[600px]">
                <x-site.media :src="$featured->gallery[0] ?? $featured->hero_image" :alt="$featured->t('name')" />
            </div>
        </section>
    @endif

    {{-- 4. Номера --}}
    @if ($featured && $rooms->isNotEmpty())
        <section class="bg-white">
            <div class="container-site flex flex-col gap-12 py-24">
                <x-site.section-head :eyebrow="__('site.hotel.rooms')" :title="__('site.home.rooms_title')" :link="route('destinations.rooms', ['hotel' => $featured->slug])" :link-label="__('site.hotel.all_rooms')" />
                @foreach ($rooms->take(1) as $room)
                    <div class="flex flex-col bg-snow lg:flex-row">
                        <a href="{{ $room->url() }}" class="aspect-[4/3] lg:aspect-auto lg:min-h-[520px] lg:flex-[1.4]" tabindex="-1" aria-hidden="true">
                            <x-site.media :src="$room->image" :alt="$room->t('name')" />
                        </a>
                        <div class="flex flex-1 flex-col justify-center gap-6 p-8 lg:p-14">
                            <h3 class="font-display text-4xl leading-tight"><a href="{{ $room->url() }}" class="hover:text-ember">{{ $room->t('name') }}</a></h3>
                            <p class="lead">{{ $room->t('summary') }}</p>
                            <ul class="flex flex-wrap gap-x-6 gap-y-2 text-sm text-slate">
                                @foreach (array_slice($room->amenityList(), 0, 4) as $a)<li>{{ $a }}</li>@endforeach
                            </ul>
                            <div class="flex flex-wrap items-center gap-7 pt-2">
                                @if ($room->price_from)
                                    <span class="text-[15px] text-slate">{{ __('site.hotel.from') }} <strong class="text-2xl font-normal text-ink">{{ price($room->price_from) }} ₸</strong> / {{ __('site.hotel.per_night') }}</span>
                                @endif
                                <a href="{{ $room->bookingUrl() }}" class="btn-dark">{{ __('site.hotel.check_prices') }}</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- 5. Впечатления --}}
    @if ($experiences->isNotEmpty())
        <section class="container-site flex flex-col gap-12 py-24">
            <x-site.section-head :eyebrow="__('site.nav.experiences')" :title="__('site.home.experiences_title')" :link="route('experiences.index')" />
            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($experiences->take(3) as $e)
                    <x-site.card :href="$e->url()" :image="$e->image" :title="$e->t('title')" :meta="$e->seasonLabels() ?: $e->categoryLabel()" :text="$e->t('summary')" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- 6. Спецпредложения --}}
    @if ($offers->isNotEmpty())
        <section class="bg-sand">
            <div class="container-site flex flex-col gap-12 py-24">
                <x-site.section-head :eyebrow="__('site.nav.offers')" :title="__('site.home.offers_title')" :link="route('offers.index')" />
                <div class="grid gap-8 md:grid-cols-3">
                    @foreach ($offers as $o)
                        <x-site.card :href="$o->url()" :image="$o->image" :title="$o->t('title')" :text="$o->t('summary')" ratio="aspect-[3/2]" :cta="__('site.common.book_now')" :cta-href="$o->bookingUrl()" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- 7. Философия бренда --}}
    @if ($brandPage)
        <section class="bg-forest text-snow">
            <div class="container-site flex max-w-4xl flex-col items-center gap-7 py-28 text-center md:py-36">
                <p class="eyebrow-light">{{ $brandPage->t('title') }}</p>
                <p class="font-display text-7xl tracking-[0.04em] md:text-[120px] md:leading-none">{{ $word['word'] ?? 'Тәңір' }}</p>
                @if (! empty($word['meaning']))<p class="text-sm uppercase tracking-[0.3em] text-snow/80">{{ $word['meaning'] }}</p>@endif
                @if (! empty($word['text']))<p class="font-display text-2xl italic leading-relaxed md:text-[34px]">{{ $word['text'] }}</p>@endif
                <a href="{{ $brandPage->url() }}" class="link-line text-snow">{{ __('site.common.learn_more') }}</a>
            </div>
        </section>
    @endif

    {{-- 8. Sustainability --}}
    @if ($sustainabilityPage && $pillars)
        <section class="container-site flex flex-col gap-12 py-24">
            <x-site.section-head :eyebrow="$sustainabilityPage->t('title')" :title="$pillars['heading'] ?? $sustainabilityPage->t('summary')" :link="$sustainabilityPage->url()" :link-label="__('site.common.learn_more')" />
            <ol class="grid border-t border-mist sm:grid-cols-2 lg:grid-cols-4">
                @foreach (array_slice($pillars['items'] ?? [], 0, 4) as $i => $item)
                    <li class="flex flex-col gap-3 border-mist py-8 sm:pr-8 lg:border-l lg:px-8 lg:first:border-l-0 lg:first:pl-0">
                        <span class="font-display text-4xl text-ember">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3 class="text-lg uppercase tracking-[0.06em]">{{ $item['title'] ?? '' }}</h3>
                        @if (! empty($item['text']))<p class="leading-relaxed text-slate">{{ $item['text'] }}</p>@endif
                    </li>
                @endforeach
            </ol>
        </section>
    @endif

    {{-- 9. Истории + «Скоро» (On the horizon у Six Senses) --}}
    <section class="bg-white">
        <div class="container-site flex flex-col gap-12 py-24 lg:flex-row lg:gap-16">
            @if ($stories->isNotEmpty())
                <div class="flex flex-[1.4] flex-col gap-10">
                    <x-site.section-head :title="__('site.home.stories_title')" :link="route('stories.index')" />
                    <div class="grid gap-8 sm:grid-cols-2">
                        @foreach ($stories->take(2) as $s)
                            <x-site.card :href="$s->url()" :image="$s->image" :title="$s->t('title')" :meta="$s->published_at?->translatedFormat('j F Y')" ratio="aspect-[3/2]" />
                        @endforeach
                    </div>
                </div>
            @endif
            <div class="flex flex-1 flex-col justify-center gap-5 bg-snow p-10 lg:p-12">
                <p class="eyebrow text-stone">{{ __('site.hotel.coming_soon') }}</p>
                <h2 class="font-display text-4xl leading-tight">{{ Setting::text('brand.newsletter_heading') ?? __('site.home.coming_title') }}</h2>
                @forelse ($comingSoon as $h)
                    <a href="{{ $h->url() }}" class="flex items-baseline justify-between gap-4 border-t border-mist pt-4 hover:text-ember">
                        <span class="font-display text-2xl">{{ $h->t('name') }}</span>
                        <span class="text-sm text-stone">{{ $h->t('opening_label') }}</span>
                    </a>
                @empty
                    <p class="leading-relaxed text-slate">{{ __('site.home.coming_text') }}</p>
                @endforelse
                {{-- Подписка: подключить к Bitrix24 / сервису рассылок --}}
                <form class="mt-2 flex flex-wrap" action="#" method="post" onsubmit="return false">
                    <label for="newsletter-email" class="sr-only">{{ __('site.common.email') }}</label>
                    <input id="newsletter-email" type="email" required autocomplete="email" placeholder="{{ __('site.common.email') }}" class="min-h-12 flex-1 basis-56 border border-[#C9C6BD] bg-white px-4 text-[15px]">
                    <button type="submit" class="btn-dark">{{ __('site.common.subscribe') }}</button>
                </form>
            </div>
        </div>
    </section>
</x-layouts.site>

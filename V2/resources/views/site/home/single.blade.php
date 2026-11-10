{{-- Главная в режиме одного отеля: весь сайт — сайт этого отеля. --}}
@php use App\Models\Setting; use App\Support\Locales; @endphp
<x-layouts.site :seo="$seo" :hotel="$hotel">

    <x-site.page-hero tall :image="$hotel?->seasonHero()" :video="media($hotel?->hero_video)"
        :eyebrow="$hotel?->t('hero_line')"
        :title="Setting::text('brand.tagline') ?? $hotel?->t('tagline') ?? 'Tenir'"
        :text="$hotel?->t('summary')" />

    @if ($hotel?->isOpen())
        <div class="container-site relative z-10 lg:-mt-14">
            <x-site.booking-bar :hotel="$hotel" id="home" />
        </div>
    @endif

    @if ($hotel)
        {{-- Об отеле --}}
        <section class="band band-snow container-site flex flex-col gap-10 md:gap-16 py-14 md:py-20 lg:py-24 lg:flex-row lg:items-center lg:gap-20">
            <div class="flex flex-1 flex-col gap-7">
                <p class="eyebrow">{{ $hotel->t('name') }}</p>
                <h2 class="h-section">{{ $hotel->t('tagline') }}</h2>
                <div class="prose-tenir">{!! $hotel->t('description') !!}</div>
                @if ($hotel->highlights)
                    <dl class="flex flex-wrap gap-x-12 gap-y-6 border-t border-mist pt-6">
                        @foreach ($hotel->highlights as $hl)
                            <div class="flex flex-col-reverse gap-1">
                                <dt class="text-[13px] uppercase tracking-[0.12em] text-stone">{{ Locales::pick($hl['label'] ?? []) }}</dt>
                                <dd class="font-display text-[44px] leading-none text-forest">{{ $hl['value'] }}</dd>
                            </div>
                        @endforeach
                    </dl>
                @endif
                <a href="{{ $hotel->sectionUrl('getting-here') }}" class="link-line self-start">{{ __('site.hotel.getting_here') }}</a>
            </div>
            <div class="aspect-[5/6] w-full flex-1 lg:max-w-[600px]">
                <x-site.media :src="$hotel->intro_image ?: ($hotel->gallery[0] ?? $hotel->hero_image)" :alt="$hotel->t('name')" />
            </div>
        </section>
    @endif

    @include('site.home.partials.rooms')

    {{-- Ресторан --}}
    @if ($venues->isNotEmpty())
        @php($venue = $venues->first())
        <section class="band band-snow container-site flex flex-col gap-8 md:gap-12 py-14 md:py-20 lg:py-24 lg:flex-row-reverse lg:items-center lg:gap-20">
            <div class="flex flex-1 flex-col gap-6">
                <p class="eyebrow">{{ __('site.hotel.dining') }}</p>
                <h2 class="h-section">{{ $venue->t('name') }}</h2>
                <p class="lead">{{ $venue->t('summary') }}</p>
                @if ($venue->t('hours'))<p class="text-[15px] text-stone">{{ $venue->t('hours') }}</p>@endif
                <a href="{{ $hotel->sectionUrl('dining') }}" class="link-line self-start">{{ __('site.common.learn_more') }}</a>
            </div>
            <div class="aspect-[4/3] w-full flex-1"><x-site.media :src="$venue->image" :alt="$venue->t('name')" /></div>
        </section>
    @endif

    @include('site.home.partials.experiences')
    @include('site.home.partials.offers')
    @include('site.home.partials.events')
    @if (\App\Models\Setting::get('gifts.on_home', false) === true)<x-site.gifts />@endif
    <x-site.recognition />


    @include('site.home.partials.stories-horizon')
</x-layouts.site>

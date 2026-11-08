@props(['seo' => null, 'hotel' => null])
@php
    use App\Models\Setting;
    use App\Support\Locales;
    use App\Support\Seo;

    $seo ??= Seo::make();
    $alternates = Seo::alternates();
    $locale = app()->getLocale();
    $ogLocale = fn (string $c) => ['ru' => 'ru_RU', 'kk' => 'kk_KZ', 'en' => 'en_US', 'zh' => 'zh_CN'][$c] ?? str_replace('-', '_', Locales::all()[$c]['hreflang']);
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $seo->fullTitle() }}</title>
    @if ($description = $seo->metaDescription())
        <meta name="description" content="{{ $description }}">
    @endif
    @if ($keywords = $seo->metaKeywords())
        <meta name="keywords" content="{{ $keywords }}">
    @endif
    @if ($seo->noindex)
        <meta name="robots" content="noindex, follow">
    @else
        <link rel="canonical" href="{{ Seo::canonical() }}">
        @foreach ($alternates as $code => $href)
            <link rel="alternate" hreflang="{{ Locales::all()[$code]['hreflang'] }}" href="{{ $href }}">
        @endforeach
        @isset($alternates[Locales::default()])
            <link rel="alternate" hreflang="x-default" href="{{ $alternates[Locales::default()] }}">
        @endisset
    @endif

    <meta property="og:type" content="{{ $seo->type }}">
    <meta property="og:site_name" content="{{ \App\Models\Setting::text('brand.name', 'Tenir Hotels') }}">
    <meta property="og:title" content="{{ $seo->fullTitle() }}">
    @if ($description)<meta property="og:description" content="{{ $description }}">@endif
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:locale" content="{{ $ogLocale($locale) }}">
    @foreach (Locales::codes() as $alt)
        @continue($alt === $locale)
        <meta property="og:locale:alternate" content="{{ $ogLocale($alt) }}">
    @endforeach
    @if ($image = $seo->imageUrl())
        <meta property="og:image" content="{{ $image }}">
        <meta property="og:image:alt" content="{{ $seo->imageAlt ?: $seo->fullTitle() }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:image" content="{{ $image }}">
    @else
        <meta name="twitter:card" content="summary">
    @endif
    <meta name="twitter:title" content="{{ $seo->fullTitle() }}">
    @if ($description)<meta name="twitter:description" content="{{ $description }}">@endif

    <link rel="icon" href="/favicon.ico" sizes="any">
    <meta name="theme-color" content="#1F3A2E">
    {{-- Шрифты грузим заранее и показываем текст сразу нужным шрифтом (без «прыжка» с запасного) --}}
    <link rel="preload" href="/fonts/GillSansProCyrillic-Medium.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/fonts/GillSansProCyrillic-Bold.woff2" as="font" type="font/woff2" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @php
        $crumbs = null;
        if (! request()->routeIs('home') && ! $seo->noindex) {
            $trail = [[Setting::text('brand.name', 'Tenir Hotels'), route('home', ['locale' => $locale])]];
            if ($hotel && \App\Support\Property::isMulti()) {
                $trail[] = [$hotel->t('name'), $hotel->url()];
            }
            $trail[] = [$seo->title ?: $seo->fullTitle(), url()->current()];
            $crumbs = $seo->breadcrumbs($trail);
        }
    @endphp
    @foreach (array_filter([...$seo->schema, $crumbs]) as $schema)
        <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
    @endforeach

    {{ $head ?? '' }}

    @foreach (['google' => 'google-site-verification', 'yandex' => 'yandex-verification', 'meta' => 'facebook-domain-verification'] as $key => $metaName)
        @if ($v = trim((string) Setting::get('analytics.verify_'.$key)))
            <meta name="{{ $metaName }}" content="{{ $v }}">
        @endif
    @endforeach
    <x-site.analytics />
    @if ($custom = Setting::get('analytics.custom_head')){!! $custom !!}@endif
</head>
<body @class(['min-h-screen', 'pb-[88px] lg:pb-0' => ! request()->attributes->get('tenir.exely_widget') && ! request()->routeIs('map')])>
    <a href="#content" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:bg-white focus:px-4 focus:py-2">{{ __('site.nav.skip') }}</a>

    <x-site.header :alternates="$alternates" />

    <main id="content">
        {{ $slot }}
    </main>

    <x-site.footer />

    {{-- Телефон: на страницах без виджета Exely — одна кнопка внизу, ведёт на страницу бронирования
         (там календарь с ценами). На главной и странице отеля есть плавающая кнопка Exely «Узнать цены». --}}
    @if (! request()->routeIs('booking', 'map') && ! request()->attributes->get('tenir.exely_widget'))
        <div class="fixed inset-x-0 bottom-0 z-30 bg-white px-4 pb-5 pt-3 shadow-[0_-8px_24px_rgba(22,33,27,0.12)] lg:hidden">
            <a href="{{ route('booking') }}" class="btn-primary w-full min-h-[52px]">{{ __('site.nav.book') }}</a>
        </div>
    @endif
    @if ($customBody = Setting::get('analytics.custom_body')){!! $customBody !!}@endif
</body>
</html>

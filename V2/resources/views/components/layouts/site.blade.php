@props(['seo' => null, 'hotel' => null])
@php
    use App\Models\Setting;
    use App\Support\Locales;
    use App\Support\Seo;

    $seo ??= Seo::make();
    $alternates = Seo::alternates();
    $locale = app()->getLocale();
    $ga4 = Setting::get('analytics.ga4');
    $metrika = Setting::get('analytics.yandex_metrika');
    $gtm = Setting::get('analytics.gtm');
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

    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $seo->fullTitle() }}">
    @if ($description)<meta property="og:description" content="{{ $description }}">@endif
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:locale" content="{{ str_replace('-', '_', Locales::all()[$locale]['hreflang']) }}">
    @if ($image = $seo->imageUrl())
        <meta property="og:image" content="{{ $image }}">
        <meta name="twitter:card" content="summary_large_image">
    @endif

    <link rel="icon" href="/favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;1,400&display=swap" rel="stylesheet">
    <link rel="preload" href="/fonts/GillSansProCyrillic-Medium.woff2" as="font" type="font/woff2" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @foreach ($seo->schema as $schema)
        <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
    @endforeach

    {{ $head ?? '' }}

    @if ($gtm)
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','{{ $gtm }}');</script>
    @endif
    @if ($ga4)
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga4 }}"></script>
        <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','{{ $ga4 }}');</script>
    @endif
    @if ($metrika)
        <script>(function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})(window,document,"script","https://mc.yandex.ru/metrika/tag.js","ym");ym({{ (int) $metrika }},"init",{clickmap:true,trackLinks:true,accurateTrackBounce:true,webvisor:true});</script>
    @endif
</head>
<body class="min-h-screen pb-[88px] lg:pb-0">
    <a href="#content" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:bg-white focus:px-4 focus:py-2">{{ __('site.nav.skip') }}</a>

    <x-site.header :alternates="$alternates" />

    {{-- Плашка Exely под шапкой на всех страницах, кроме самой страницы бронирования --}}
    @unless (request()->routeIs('booking'))
        <x-site.booking-bar :hotel="$hotel" />
    @endunless

    <main id="content">
        {{ $slot }}
    </main>

    <x-site.footer />

    {{-- Бронирование: нижняя панель на телефоне ведёт на страницу с модулем Exely --}}
    @unless (request()->routeIs('booking'))
    <div class="fixed inset-x-0 bottom-0 z-30 flex items-center gap-3 bg-white px-4 pb-6 pt-3 shadow-[0_-8px_24px_rgba(22,33,27,0.12)] lg:hidden">
        <a href="{{ route('booking') }}" class="flex min-h-[52px] flex-1 flex-col justify-center border border-[#C9C6BD] px-4">
            <span class="field-label">{{ __('site.booking.check_in') }} — {{ __('site.booking.check_out') }}</span>
            <span class="text-[15px]">{{ $hotel?->t('name') ?? \App\Models\Setting::text('brand.name', 'Tenir Hotels') }}</span>
        </a>
        <a href="{{ route('booking') }}" class="btn-primary min-h-[52px] px-5">{{ __('site.nav.book') }}</a>
    </div>
    @endunless
</body>
</html>

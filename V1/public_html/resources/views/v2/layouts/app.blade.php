<!DOCTYPE html>
<html lang="ru" >
<head>
    <meta charset="UTF-8">
    <title>Tenir</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="favicon.ico"><link href="/css/app.css" rel="stylesheet">

    <!-- Yandex.Metrika counter -->
    <script type="text/javascript" >
        (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
            m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
        (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

        ym(69916861, "init", {
            clickmap:true,
            trackLinks:true,
            accurateTrackBounce:true
        });
    </script>
    <noscript><div><img src="https://mc.yandex.ru/watch/69916861" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
    <!-- /Yandex.Metrika counter -->

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-5QTC3FKSJV"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-5QTC3FKSJV');
    </script><!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-5QTC3FKSJV"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-5QTC3FKSJV');
    </script>
</head>
@if(request()->route()->getName() == 'home')
    <body class="main-page" data-spy="scroll" data-target="#scrollSpy" data-offset="0">
@else
    <body>
@endif
<div class="preload"></div>
<header class="header">
    <div class="header-container py-4">
        <nav class="navbar container">
            <div class="row align-items-center">
                <div class="navbar-logo col col-xl-3">
                    <a href="/">
                        <img src="/images/content/logo.svg" alt="Tenir">
                        <img src="/images/content/logo-dark.svg" class="dark" alt="Tenir">
                    </a>
                </div>
                <div class="navbar-menu d-none d-lg-block col col-xl-6">
                    <ul class="d-flex">
                        <li class="mr-4 {{ request()->route()->getName() == 'hotel.index' ? 'active' : '' }}">
                            <a href="{{ route('hotel.index') }}">
                                {{ translate('otel') }}
                            </a>
                        </li>
                        <li class="mr-4 {{ request()->route()->getName() == 'activities.index' ? 'active' : '' }}">
                            <a href="{{ route('activities.index') }}">
                                {{ translate('aktivnosti') }}
                            </a>
                        </li>
                        <li class="mr-4 {{ request()->route()->getName() == 'about.index' ? 'active' : '' }}">
                            <a href="{{ route('about.index') }}">
                                {{ translate('nasha-filsofofiya') }}
                            </a>
                        </li>
                        <li class="mr-4 {{ request()->route()->getName() == 'news.index' ? 'active' : '' }}">
                            <a href="{{ route('news.index') }}">
                                {{ translate('novosti') }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="navbar-support d-none d-lg-flex col col-xl-3 align-items-center">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#callback-modal">
                        Зарезервировать
                    </button>

                    <div style="cursor:pointer;" class="navbar-lang ml-auto">
                        @foreach(config('app.languages') as $key => $name)
                            @if($key == app()->getLocale())
                                <span class="active">{{ $name }}</span>
                            @endif
                        @endforeach
                        @foreach(config('app.languages') as $key => $name)
                            @if($key != app()->getLocale())
                                <a href="{{ route('lang.update', $key ) }}">{{ $name }}</a>
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="navbar-toggle d-flex d-lg-none col col-2 align-items-center justify-content-end">
                    <button  data-toggle="modal" data-target="#mobile-nav">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                </div>
            </div>
        </nav>
    </div>
</header>

<div class="modal fade" id="mobile-nav" tabindex="-1" role="dialog" aria-labelledby="mobileNav" aria-hidden="true">
    <div class="modal-dialog fade-right modal-callback" role="document">
        <div class="modal-content mobile-menu">
            <div class="modal-header mobile-nav-header">
                <div class="mobile-nav-logo mx-auto">
                    <a href="/">
                        <img src="/images/content/footer-logo.svg" alt="Tenir">
                    </a>
                </div>
                <button type="button" class="ml-auto close modal-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body mobile-nav-body">
                <ul class="mobile-nav-menu">
                    <li>
                        <a href="{{ route('hotel.index') }}">
                            {{ translate('otel') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('activities.index') }}">
                            {{ translate('aktivnosti') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('about.index') }}">
                            {{ translate('nasha-filsofofiya') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('news.index') }}">
                            {{ translate('novosti') }}
                        </a>
                    </li>
                </ul>

                <a class="footer-link"  target="_blank" href="{{ $settings->data['offer'] ?? '#' }}">{{ translate('polozheniya') }}</a>

                <div class="navbar-lang my-4">
                    @foreach(config('app.languages') as $key => $name)
                        <a href="{{ route('lang.update', $key ) }}">{{ $name }}</a>
                    @endforeach
                </div>

                <div class="footer-soc my-3">
                    <a href="{{ $settings->data['instagram'] }}" class="mx-3 soc soc-grey soc-inst soc-rounded"></a>
                    <a href="{{ $settings->data['facebook'] }}" class="mx-3 soc soc-grey soc-fa soc-rounded"></a>
                    <a href="https://soundcloud.com/user-255415572" class="mx-3 soc soc-grey soc-soundcloud soc-rounded"></a>
                </div>

                <button class="btn btn-fill" data-toggle="modal" data-target="#callback-modal">
                    {{ translate('zarezirvirovat') }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="callback-modal" tabindex="-1" role="dialog" aria-labelledby="callbackForm" aria-hidden="true">
    <div class="modal-dialog fade-right modal-callback" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel">Зарезервируйте свое проживание</h5>
                    <button type="button" class="close modal-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="callback-form" class="form-callback">
                    <div class="form-callback-inputs">
                        <div class="row">
                            <div class="col-12">
                                <div class="input-wrap">
                                    <input type="text" id="name" name="name" required>
                                    <label for="name" class="input-label">
                                        Ф.И.О.
                                    </label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="input-wrap">
                                    <input type="datetime-local" id="date-in" name="date-in" required>
                                    <label for="date-in" class="input-label">
                                        {{ translate('data-zaezda') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="input-wrap">
                                    <input type="datetime-local" id="date-out" name="date-out" required>
                                    <label for="date-out" class="input-label">
                                        Дата отъезда
                                    </label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="input-wrap">
                                    <input type="tel" id="phone" name="phone" required>
                                    <label for="phone" class="input-label">
                                        Phone
                                    </label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="input-wrap">
                                    <input type="email" id="email" name="email" required>
                                    <label for="email" class="input-label">
                                        e-mail
                                    </label>
                                </div>
                            </div>

                            <div class="col-12 mt-2 mt-md-5 d-flex justify-content-center">
                                <div class="input-checkbox">
                                    <input type="checkbox" name="conditions" id="conditions" required>
                                    <label class="input-checkbox-custom" for="conditions"></label>
                                    <span class="input-checkbox-label">
                        <a href="#">{{ translate('polozheniya') }}</a>
                      </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mt-5 callback-form-footer text-center">
                        <button class="btn btn-fill">
                            {{ translate('zarezirvirovat') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="scene-overlay">
    <div class="container">
        <div class="row">
            <div class="scene-soc">
                <a href="{{ $settings->data['facebook'] }}"  class="mt-3 soc soc-white soc-fa"></a>
                <a href="{{ $settings->data['instagram'] }}" class="mt-3 soc soc-white soc-inst"></a>
                <a href="https://soundcloud.com/user-255415572" class="mt-3 soc soc-white soc-soundcloud"></a>
            </div>
            <div class="scene-scroll">
                <div class="scene-scroll-bullets" id="scrollSpy">
                    <span data-scene="scene-1" class="scene-scroll-bullet active"></span>
                    <span data-scene="scene-2" class="scene-scroll-bullet"></span>
                    <span data-scene="scene-3" class="scene-scroll-bullet"></span>
                    <span data-scene="scene-4" class="scene-scroll-bullet"></span>
                </div>
                <div class="scene-scroll-text">
                    {{ translate('prokrutite-vniz') }}
                </div>
            </div>
        </div>
    </div>
</div>
@yield('content')
<script src="/js/app.js"></script></body>
</html>

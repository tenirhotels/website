<!DOCTYPE html>

<html lang="{{ Lang::locale() }}">
<head>
    <meta charset="UTF-8">
    <title>
        @if (request()->route()->getName() == 'booking.index')
            {{ translate('booking-index') }}
        @elseif(request()->route()->getName() == 'home')
            {{ translate('home-meta-title') }}
        @else
            @yield('title', $settings->data['title'] ?? '')
        @endif
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/favicon.ico">
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <meta name="description" content="@yield('description', $settings->data['description'] ?? '')">
    <meta name="keywords" content="@yield('keywords',  $settings->data['keywords'] ?? '')">

    <!-- Yandex.Metrika counter -->
    <script type="text/javascript">
        (function (m, e, t, r, i, k, a) {
            m[i] = m[i] || function () {
                (m[i].a = m[i].a || []).push(arguments)
            };
            m[i].l = 1 * new Date();
            k = e.createElement(t), a = e.getElementsByTagName(t)[0], k.async = 1, k.src = r, a.parentNode.insertBefore(k, a)
        })
        (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

        ym(69916861, "init", {
            clickmap: true,
            trackLinks: true,
            accurateTrackBounce: true,
            webvisor: true
        });
    </script>
    <noscript>
        <div><img src="https://mc.yandex.ru/watch/69916861" style="position:absolute; left:-9999px;" alt=""/></div>
    </noscript>
    <!-- /Yandex.Metrika counter -->

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-5QTC3FKSJV"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());

        gtag('config', 'G-5QTC3FKSJV');
    </script>

    {{--
    <!-- start TL head script -->
    <script type='text/javascript'>
        (function(w) {
            var q = [
                ['setContext', 'TL-INT-tenirhotels', '{{ Lang::locale() }}']
            ];
            var t = w.travelline = (w.travelline || {}),
                ti = t.integration = (t.integration || {});
            ti.__cq = ti.__cq? ti.__cq.concat(q) : q;
            if (!ti.__loader) {
                ti.__loader = true;
                var d = w.document,
                    p = d.location.protocol,
                    s = d.createElement('script');
                s.type = 'text/javascript';
                s.async = true;
                s.src = (p == 'https:' ? p : 'http:') + '//ibe.tlintegration.com/integration/loader.js';
                (d.getElementsByTagName('head')[0] || d.getElementsByTagName('body')[0]).appendChild(s);
            }
        })(window);
    </script>
    <!-- end TL head script -->
    --}}
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
                                <img src="{{ asset('images/content/logo.svg') }}" alt="Tenir Eco Hotel">
                                <img src="{{ asset('images/content/logo-dark.svg') }}" class="dark" alt="Tenir Eco Hotel">
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
                                {{ translate('zarezirvirovat') }}
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
                            <button data-toggle="modal" data-target="#mobile-nav">
                                <span></span>
                                <span></span>
                                <span></span>
                            </button>
                        </div>
                    </div>
                </nav>
            </div>
        </header>

        <div class="modal fade" id="mobile-nav" tabindex="-1" role="dialog" aria-labelledby="mobileNav"
             aria-hidden="true">
            <div class="modal-dialog fade-right modal-callback" role="document">
                <div class="modal-content mobile-menu">
                    <div class="modal-header mobile-nav-header">
                        <div class="mobile-nav-logo mx-auto">
                            <a href="/">
                                <img src="{{ asset('images/content/footer-logo.svg') }}" alt="Tenir">
                            </a>
                        </div>
                        <button type="button" class="ml-auto close modal-close" data-dismiss="modal"
                                aria-label="Close"></button>
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

                        <a class="footer-link" target="_blank"
                           href="{{ $offer ?? '#' }}">{{ translate('polozheniya') }}</a>

                        <div class="navbar-lang my-4">
                            @foreach(config('app.languages') as $key => $name)
                                <a href="{{ route('lang.update', $key ) }}">{{ $name }}</a>
                            @endforeach
                        </div>

                        <div class="footer-soc my-3">
                            <a href="{{ $settings->data['instagram'] }}" target="_blank"
                               class="soc soc-grey soc-inst soc-rounded"></a>
                            <a href="{{ $settings->data['facebook'] }}" target="_blank"
                               class="soc soc-grey soc-fa soc-rounded"></a>
							<a href="https://www.tiktok.com/@tenirhotels" target="_blank"
                               class="soc soc-grey soc-tiktok soc-rounded"></a>
                            <a href="https://soundcloud.com/user-255415572" target="_blank"
                               class="soc soc-grey soc-soundcloud soc-rounded"></a>

                        </div>

                        <button class="btn btn-fill" data-toggle="modal" data-target="#callback-modal">
                            {{ translate('zarezirvirovat') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="callback-modal" tabindex="-1" role="dialog" aria-labelledby="callbackForm"
             aria-hidden="true">
            <div class="modal-dialog fade-right modal-callback" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel">{{ translate('zabronoiruite') }}</h5>
                            <button type="button" class="close modal-close" data-dismiss="modal"
                                    aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    <!--Старт бронирования номеров планируется в ближайшее время. Следите за новостями на нашем сайте и в социальных сетях.

                <div>
                    <a href="{{ $settings->data['instagram'] }}"  target="_blank">Instagram</a>
                    <a href="{{ $settings->data['facebook'] }}"  target="_blank">Facebook</a>
                </div-->
                    <!--form id="callback-form" class="form-callback">
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
                    </form-->
                        <style>
                            #block-search {
                                background: transparent;
                                max-width: 350px;
                                margin: 30px auto 0;
                                box-sizing: border-box;
                            }
                        </style>
                        <!-- start TL Search form script -->
                        <div id='block-search'>
                            <div id='tl-search-form' class='tl-container'>
                            </div>
                        </div>
                        <script type='text/javascript'>
                            (function (w) {
                                var q = [
                                    ['setContext', 'TL-INT-tenirhotels', '{{Lang::locale()}}'],
                                    ['embed', 'search-form', {
                                        container: 'tl-search-form'
                                    }]
                                ];
                                var t = w.travelline = (w.travelline || {}),
                                    ti = t.integration = (t.integration || {});
                                ti.__cq = ti.__cq ? ti.__cq.concat(q) : q;
                                if (!ti.__loader) {
                                    ti.__loader = true;
                                    var d = w.document,
                                        p = d.location.protocol,
                                        s = d.createElement('script');
                                    s.type = 'text/javascript';
                                    s.async = true;
                                    s.src = (p == 'https:' ? p : 'http:') + '//ibe.tlintegration.com/integration/loader.js';
                                    (d.getElementsByTagName('head')[0] || d.getElementsByTagName('body')[0]).appendChild(s);
                                }
                            })(window);
                        </script>
                        <!-- end TL Search form script -->


                    </div>
                </div>
            </div>
        </div>
        @if(request()->route()->getName() == 'home')
            <div class="scene-overlay">
                <div class="container">
                    <div class="row">
                        <div class="scene-soc">
                            <a href="{{ $settings->data['facebook'] }}" target="_blank"
                               class="mt-3 soc soc-white soc-fa"></a>
                            <a href="{{ $settings->data['instagram'] }}" target="_blank"
                               class="mt-3 soc soc-white soc-inst"></a>
                            <a href="https://soundcloud.com/user-255415572" target="_blank"
                               class="mt-3 soc soc-white soc-soundcloud"></a>

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
        @endif

        @yield('content')

        @if(request()->route()->getName() !== 'home')
            <footer class="footer">
                <div class="container">
                    <div class="row">
                        <div class="col col-12 col-xl-6 col-md-4 col-sm-6 mb-4 mb-md-0 order-3 order-sm-1 text-sm-left text-center">
                            <a class="footer-logo" href="/">
                                <img src="{{ asset('images/content/footer-logo.svg') }}" alt="">
                            </a>

                            <div class="footer-copy">
                                © 2020-2021 Tenir Hotels
                            </div>
                        </div>

                        <div class="col col-12 col-xl-3 col-md-4 col-sm-6 mb-4 mb-md-0 order-2 order-sm-1 text-sm-left text-center">
                            <nav class="footer-nav">
                                <ul>
                                    <li><a href="{{ route('hotel.index') }}">{{ translate('otel') }}</a></li>
                                    <li><a href="{{ route('activities.index') }}">{{ translate('aktivnosti') }}</a></li>
                                    <li><a href="{{ route('about.index') }}">{{ translate('nasha-filsofofiya') }}</a>
                                    </li>
                                    <li><a href="{{ route('news.index') }}">{{ translate('novosti') }}</a></li>
                                </ul>
                            </nav>

                            <a class="footer-link" target="_blank"
                               href="{{ $offer ?? '#' }}">{{ translate('polozheniya') }}</a>
                        </div>

                        <div class="col col-12 col-xl-3 col-md-4 col-sm-6 mb-4 mb-md-0 order-1 order-sm-1 text-sm-left text-center">
                            <address class="footer-contact">
                                <p>{{ translate('kontakty') }}</p>
                                <p><a href="tel:{{ $settings->data['phone'] }}">{{ $settings->data['phone'] }}</a></p>
                                <p>{!! $settings->data['address'][app()->getLocale()] ?? $settings->data['address']['ru'] !!}</p>
                            </address>

                            <div class="footer-soc">
                                <a href="{{ $settings->data['instagram'] }}" target="_blank"
                                class="soc soc-grey soc-inst soc-rounded"></a>
                                <a href="{{ $settings->data['facebook'] }}" target="_blank"
                                class="soc soc-grey soc-fa soc-rounded"></a>
								<a href="https://www.tiktok.com/@tenirhotels" target="_blank" 
								class="soc soc-grey soc-tiktok soc-rounded"></a>
                                <a href="https://soundcloud.com/user-255415572" target="_blank"
                                class="soc soc-grey soc-soundcloud soc-rounded"></a>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        @endif
        <script src="{{ mix('js/app.js') }}"></script>
        </body>
</html>

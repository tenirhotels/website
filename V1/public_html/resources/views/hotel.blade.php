@extends('layouts.app')

@section('content')
    <main class="page">
        <div class="page-header page-header--medium" data-aos="fade-down" data-aos-duration="700" data-aos-once="true">
            <img src="/images/content/head_about.jpg" alt="">
        </div>
        <section class="page-section">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-md-4 col-lg-5">
                        <h1 data-aos-duration="700" ata-aos-delay="700" data-aos="fade-right" data-aos-once="true">
                            {{ translate('otel') }}</h1>
                    </div>
                    <div class="col-12 col-md-7 col-lg-6 fz-4" data-aos-duration="700" ata-aos-delay="700"
                         data-aos="fade-left" data-aos-once="true">
                        {!! $variable->data['description'][app()->getLocale()] !!}
                    </div>
                </div>
            </div>
        </section>

        <section class="page-section page-section--type-1">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-xl-3 offset-xl-1 col-md-5" data-aos="fade-right" data-aos-delay="0"
                         data-aos-duration="700" data-aos-once="true">
                        <h2 class="page-section-title mt-4">
                            Номер
                        </h2>

                        <p class="fz-3 mb-5">
                            {{ $variable->data['number'][app()->getLocale()] }}
                        </p>

                        <a href="/booking" class="btn btn-white"  data-toggle="modal" data-target="#callback-modal" >
                            {{ translate('zarezirvirovat') }}
                        </a>
                    </div>

                    <div class="col-12 col-md-7 col-xl-6 offset-xl-1" data-aos="fade-left" data-aos-duration="700"
                         data-aos-once="true">
                        <div class="room-carousel-wrap">
                            <div class="room-carousel">
                                @foreach($hotelBanners as $hotelBanner)
                                    <div class="slide">
                                        <img src="{{ $hotelBanner->url }}" alt="Отели">
                                    </div>
                                @endforeach
                            </div>
                            <div class="carousel-navigation"></div>
                            <div class="carousel-dots"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h2 class="page-section-title text-center" data-aos="fade-down" data-aos-delay="0"
                            data-aos-duration="700" data-aos-once="true">
                            {{ translate('udobstva') }}
                        </h2>
                    </div>

                    <div class="col-lg-2 col-6 col-md-4" data-aos="fade-up" data-aos-delay="0" data-aos-duration="700"
                         data-aos-once="true">
                        <div class="comfort text-center">
                            <div class="comfort-img">
                                <img class="mw-100" src="images/content/comfort-1.svg" alt="">
                            </div>
                            <h3 class="mt-1 mb-2">{{ $facilitiesVariable->data['title_media'][app()->getLocale()] }}</h3>
                            <p>
                                @foreach(!empty($facilitiesVariable->data['list_media'][app()->getLocale()]) ? $facilitiesVariable->data['list_media'][app()->getLocale()] : $facilitiesVariable->data['list_media']['ru'] as $matrix)
                                    {{ $matrix['list'] }}

                                    @if (!$loop->last)
                                        <br>
                                    @endif
                                @endforeach
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-2 col-6 col-md-4" data-aos="fade-up" data-aos-delay="100" data-aos-duration="700"
                         data-aos-once="true">
                        <div class="comfort text-center">
                            <div class="comfort-img">
                                <img class="mw-100" src="images/content/comfort-2.svg" alt="">
                            </div>
                            <h3 class="mt-1 mb-2">{{ $facilitiesVariable->data['title_foods'][app()->getLocale()] }}</h3>
                            <p>
                                @foreach(!empty($facilitiesVariable->data['list_foods'][app()->getLocale()]) ? $facilitiesVariable->data['list_foods'][app()->getLocale()] : $facilitiesVariable->data['list_foods']['ru'] as $matrix)
                                    {{ $matrix['list'] }}

                                    @if (!$loop->last)
                                        <br>
                                    @endif
                                @endforeach
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-2 col-6 col-md-4" data-aos="fade-up" data-aos-delay="200" data-aos-duration="700"
                         data-aos-once="true">
                        <div class="comfort text-center">
                            <div class="comfort-img">
                                <img class="mw-100" src="images/content/comfort-3.svg" alt="">
                            </div>
                            <h3 class="mt-1 mb-2">{{ $facilitiesVariable->data['title_bedroom'][app()->getLocale()] }}</h3>
                            <p>
                                @foreach(!empty($facilitiesVariable->data['list_bedroom'][app()->getLocale()]) ? $facilitiesVariable->data['list_bedroom'][app()->getLocale()] : $facilitiesVariable->data['list_bedroom']['ru'] as $matrix)
                                    {{ $matrix['list'] }}

                                    @if (!$loop->last)
                                        <br>
                                    @endif
                                @endforeach
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-2 col-6 col-md-4" data-aos="fade-up" data-aos-delay="300" data-aos-duration="700"
                         data-aos-once="true">
                        <div class="comfort text-center">
                            <div class="comfort-img">
                                <img class="mw-100" src="images/content/comfort-4.svg" alt="">
                            </div>
                            <h3 class="mt-1 mb-2">{{ $facilitiesVariable->data['title_bathroom'][app()->getLocale()] }}</h3>
                            <p>
                                @foreach(!empty($facilitiesVariable->data['list_bathroom'][app()->getLocale()]) ? $facilitiesVariable->data['list_bathroom'][app()->getLocale()] : $facilitiesVariable->data['list_bathroom']['ru'] as $matrix)
                                    {{ $matrix['list'] }}

                                    @if (!$loop->last)
                                        <br>
                                    @endif
                                @endforeach
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-2 col-6 col-md-4" data-aos="fade-up" data-aos-delay="400" data-aos-duration="700"
                         data-aos-once="true">
                        <div class="comfort text-center">
                            <div class="comfort-img">
                                <img class="mw-100" src="images/content/comfort-5.svg" alt="">
                            </div>
                            <h3 class="mt-1 mb-2">{{ $facilitiesVariable->data['title_sauna'][app()->getLocale()] }}</h3>
                            <p>
                                @foreach(!empty($facilitiesVariable->data['list_sauna'][app()->getLocale()]) ? $facilitiesVariable->data['list_sauna'][app()->getLocale()] : $facilitiesVariable->data['list_sauna']['ru'] as $matrix)
                                    {{ $matrix['list'] }}

                                    @if (!$loop->last)
                                        <br>
                                    @endif
                                @endforeach
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-2 col-6 col-md-4" data-aos="fade-up" data-aos-delay="500" data-aos-duration="700"
                         data-aos-once="true">
                        <div class="comfort text-center">
                            <div class="comfort-img">
                                <img class="mw-100" src="/images/content/lyzhi.svg" alt="">
                            </div>
                            <h3 class="mt-1 mb-2">{{ $facilitiesVariable->data['title_locker'][app()->getLocale()] }}</h3>
                            <p>
                                @foreach(!empty($facilitiesVariable->data['list_locker'][app()->getLocale()]) ? $facilitiesVariable->data['list_locker'][app()->getLocale()] : $facilitiesVariable->data['list_locker']['ru'] as $matrix)
                                    {{ $matrix['list'] }}

                                    @if (!$loop->last)
                                        <br>
                                    @endif
                                @endforeach
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="page-section">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <h2 class="page-section-title" data-aos="fade-up" data-aos-duration="700" data-aos-once="true">
                            Привилегии
                        </h2>
                    </div>
                </div>

                <div class="row">
                    @foreach($hotelPrivileges as $hotelPrivilege)
                        <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="0" data-aos-duration="700"
                             data-aos-once="true">
                            <div class="benefit">
                                <div class="benefit-img mw-100">
                                    <img src="{{ $hotelPrivilege->image_url }}" alt="{{ $hotelPrivilege->title }}">
                                </div>

                                <p class="benefit-text fz-4">
                                    {{ $hotelPrivilege->title }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="page-section">
            <div class="container">
                <div class="row" data-aos="fade-down" data-aos-delay="0" data-aos-duration="700" data-aos-once="true">
                    <div class="col-12">
                        <h2 class="page-section-title text-center">
                            {{ translate('infasctructura') }}
                        </h2>
                    </div>
                </div>

                <div class="infra-carousel" data-aos="fade-up" data-aos-delay="0" data-aos-duration="700"
                     data-aos-once="true">
                    <div class="row">
                        <div class="col-12 position-relative col-lg-6 infra-left px-0">
                            <div class="infra-controls">
                                <div class="infra-page">
                                    <span class="infra-page-current">1</span>
                                    <span class="infra-page-divider">/</span>
                                    <span class="infra-page-total">{{ $infrastructures->count() }}</span>
                                </div>

                                <div class="infra-arrows">
                                    <div class="infra-arrow infra-arrow-left"></div>
                                    <div class="infra-arrow infra-arrow-right"></div>
                                </div>
                            </div>
                            <div class="infra-texts">
                                @foreach($infrastructures as $infrastructure)
                                    <div class="infra-text">
                                        <h3>
                                            {{ $infrastructure->title }}
                                        </h3>
                                        <p>
                                            {{ $infrastructure->content }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 p-0">
                            <div class="infra-imgs">
                                @foreach($infrastructures as $infrastructure)
                                    <div class="slide infra-img">
                                        <img src="{{ $infrastructure->image_url }}" alt="111{{ $infrastructure->title }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="page-section">
            <div class="container">
                <div class="row" data-aos="fade-down" data-aos-delay="0" data-aos-duration="700" data-aos-once="true">
                    <div class="col-12 col-xl-4 col-md-6 mx-auto">
                        <h2 class="page-section-title text-center">
                            Дополнительные сервисы
                        </h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="0" data-aos-duration="700"
                         data-aos-once="true">
                        <div class="comfort text-center">
                            <div class="comfort-img mw-100">
                                <img src="images/content/service-1.svg" alt="">
                            </div>

                            <h3>
                                {{ $servicesVariable->data['title_rent'][app()->getLocale()] }}
                            </h3>
                        </div>
                    </div>
                    <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="200" data-aos-duration="700"
                         data-aos-once="true">
                        <div class="comfort  text-center">
                            <div class="comfort-img mw-100">
                                <img src="images/content/service-2.svg" alt="">
                            </div>

                            <h3>
                                {{ $servicesVariable->data['title_rental'][app()->getLocale()] }}
                            </h3>
                        </div>
                    </div>
                    <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="400" data-aos-duration="700"
                         data-aos-once="true">
                        <div class="comfort text-center">
                            <div class="comfort-img mw-100">
                                <img src="images/content/service-3.svg" alt="">
                            </div>

                            <h3>
                                {{ $servicesVariable->data['title_taxi'][app()->getLocale()] }}
                            </h3>
                        </div>
                    </div>
                    <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="600" data-aos-duration="700"
                         data-aos-once="true">
                        <div class="comfort text-center">
                            <div class="comfort-img mw-100">
                                <img src="images/content/service-4.svg" alt="">
                            </div>

                            <h3>
                                {{ $servicesVariable->data['title_delivery'][app()->getLocale()] }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="page-section page-section--type-2">
            <div class="container">
                <div class="row" data-aos="fade-down" data-aos-delay="0" data-aos-duration="700" data-aos-once="true">
                    <div class="col-12 col-xl-4 col-md-6  mx-auto">
                        <h2 class="page-section-title text-center">
                            {{ translate('osobennosti-prozhivaniya') }}
                        </h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-5 col-lg-4 col-xl-3 offset-xl-2 offset-lg-1" data-aos="fade-down"
                         data-aos-delay="0" data-aos-duration="700" data-aos-once="true">
                        <h3 class="my-4 color-dark">
                            {{ translate('kak-dobratsya-do-tenir') }}
                        </h3>
                        <div class="fz-3">
                            {!! $variable->data['how_to_get'][app()->getLocale()] !!}
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-5 col-xl-4 offset-xl-2 offset-md-1" data-aos="fade-down"
                         data-aos-delay="0" data-aos-duration="700" data-aos-once="true">
                        <h3 class="my-4 color-dark">
                            {{ translate('ostanovki-kanatnoy-dorogi-i-vremya-raboti') }}
                        </h3>
                        <ol>
                            @foreach(!empty($variable->data['schedules'][app()->getLocale()]) ? $variable->data['schedules'][app()->getLocale()] : $variable->data['schedules']['ru'] as $schedule)
                                <li>
                                    {{ $schedule['content'] }}
                                </li>
                            @endforeach
                        </ol>
                    </div>

                    <div class="col-12 col-md-5 col-lg-4 offset-xl-2 offset-lg-1 pt-5" data-aos="fade-down"
                         data-aos-delay="0" data-aos-duration="700" data-aos-once="true">
                        <p class="fz-5">
                            {{ translate('nastoyatelno-rekomenduem-oznakomitsya-so-sleduyushej-informaciej') }}
                        </p>
                    </div>
                    <div class="fz-3 col-12 col-md-6 col-lg-5 col-xl-4 offset-xl-1 offset-md-1 pt-5"
                         data-aos="fade-down" data-aos-delay="0" data-aos-duration="700" data-aos-once="true">
                        {!!
                            !empty( $variable->data['rules'][app()->getLocale()]) ?  $variable->data['rules'][app()->getLocale()] :  $variable->data['rules']['ru']
                       !!}
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection

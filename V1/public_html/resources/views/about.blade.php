@extends('layouts.app')

@section('title')
    {{ translate('nasha-filsofofiya') }}
@endsection
@section('content')
    <main class="page">
        <div class="page-header" data-aos="fade-down" data-aos-duration="700" data-aos-once="true">
            <img src="{{$variable->data['header'] ?? 'images/content/phil-header-img.png' }}" alt="">
            <div class="page-header-title">
                <div class="container">
                    <div class="row">
                        <div class="col-12 offset-lg-1 col-lg-11">
                            <h1 data-aos="fade-down" data-aos-duration="700" data-aos-delay="700" data-aos-once="true">
                                {{ translate('nasha-filsofofiya') }}
                            </h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <section class="page-section">
            <div class="container">
                <div class="row">
                    <div class="d-none d-sm-block col-4 offset-lg-1 aos-init aos-animate" data-aos="fade-down"
                         data-aos-duration="700" data-aos-delay="900"
                         data-aos-once="true">
                        <img src="/images/content/phil-img.png" alt="">
                    </div>
                    <div class="col-12 col-xl-4 col-lg-5 offset-lg-1 fz-3" data-aos="fade-down" data-aos-duration="700"
                         data-aos-delay="1100" data-aos-once="true">
                        <h2 class="page-section-title">
                            {{
                                    !empty($variable->data['title_one'][app()->getLocale()]) ?  $variable->data['title_one'][app()->getLocale()] : $variable->data['title_one']['ru']
                            }}
                        </h2>
                        {!!
                            !empty($variable->data['content_one'][app()->getLocale()]) ? $variable->data['content_one'][app()->getLocale()] : $variable->data['content_one']['ru']
                        !!}
                    </div>
                </div>
            </div>
        </section>

        <section class="page-section">
            <div class="container">
                <div class="row">
                    <div class="col-12 offset-xl-2 offset-lg-1 col-lg-6 col-xl-5 pr-xl-5 fz-3" data-aos="fade-right"
                         data-aos-duration="700" data-aos-once="true">
                        <h2 class="page-section-title fz-6">
                            {{ !empty($variable->data['title_two'][app()->getLocale()]) ? $variable->data['title_two'][app()->getLocale()] : $variable->data['title_two']['ru'] }}
                        </h2>
                        {!! !empty($variable->data['content_two'][app()->getLocale()]) ? $variable->data['content_two'][app()->getLocale()] : $variable->data['content_two']['ru'] !!}
                    </div>

                    <div class="col-12 offset-lg-1 col-lg-4 col-xl-3" data-aos="fade-left" data-aos-duration="700"
                         data-aos-once="true">
                        <h2 class="page-section-title d-none d-lg-block">
                            &nbsp;<br>&nbsp;<br>
                        </h2>
                        <p class="fz-5">
                            {{
                                !empty( $variable->data['last_title'][app()->getLocale()]) ?  $variable->data['last_title'][app()->getLocale()] :  $variable->data['last_title']['ru']
                            }}
                        </p>
                        <div class="row mt-5">
                            <div class="col-4">
                                <img src="{{ asset('images/content/phil-thumb-1.svg') }}" alt="">
                            </div>
                            <div class="col-4">
                                <img src="{{ asset('images/content/phil-thumb-2.svg') }}" alt="">
                            </div>
                            <div class="col-4">
                                <img src="{{ asset('images/content/phil-thumb-3.svg') }}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection

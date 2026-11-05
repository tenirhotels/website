@extends('layouts.app')

@section('title')
    {{ translate('aktivnosti') }}
@endsection
@section('content')
    <main class="page activities-page" style="background-image: url('{{ $setting->data['activities'] ?? '/images/design/activity-bg.33f439.png' }}')">
        <section class="scene scene-5">
            <div class="container activities-title-wrap">
                <div class="row">
                    <div class="col-12 col-xl-4 offset-xl-1 col-md-5">
                        <h1 class="activities-title" data-aos-once="true" data-aos="fade-up" data-aos-duration="1000"
                        style="font-size: 40pt">
                            {{ translate('cplanirujte-svoj-otdyh-na-shymbulakeniya') }}
                        </h1>
                    </div>
                </div>
            </div>
            <div class="activities-list-wrap-parent">
                <div class="activities-list-wrap" data-aos-once="true" data-aos="fade-up" data-aos-delay="500"
                     data-aos-duration="1000">
                    <div class="activities-scroll">
                        <div class="container">
                            <div class="row">
                                <div class="col-12 col-xl-4 offset-xl-1 col-md-4"></div>
                                <div class="col-12 col-xl-5 offset-md-1 col-md-6">
                                    <div class="activities-list">
                                        @foreach($activities as $activity)
                                            <div class="activities-item" data-toggle="modal"
                                                 data-target="#activity-{{ $activity->id }}">
                                                <div class="activities-item-text">{{ $activity->title }}</div>
                                                <div class="activities-item-img">
                                                    <img src="{{ $activity->image_url }}">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            @foreach($activities as $activity)
                <div class="modal fade" id="activity-{{ $activity->id }}" tabindex="-1" role="dialog"
                     aria-labelledby="callbackForm"
                     aria-hidden="true">
                    <div class="modal-dialog activity-modal fade-right" role="document">
                        <div class="modal-content activity-modal-content">
                            <button type="button" class="close modal-close modal-close-white activity-modal-close"
                                    data-dismiss="modal" aria-label="Close"></button>
                            <div class="activity-carousel-wrap">
                                <div class="activity-carousel">
                                    @foreach($activity->sliders as $image)
                                        <div class="slide">
                                            <img src="{{ $image->url }}" alt="">
                                        </div>
                                    @endforeach
                                </div>
                                <div class="activity-carousel-nav carousel-navigation"></div>
                            </div>
                            <div class="activity-modal-body">
                                <h2>{{ $activity->title }}</h2>
                                {!! $activity->content !!}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </section>
    </main>
@endsection

@extends('layouts.app')

@section('title', $post->title)
@section('content')
    <main class="page">
        <div class="page-header page-header--big" data-aos="fade-down" data-aos-duration="700" data-aos-once="true">
            <img src="{{ $post->photo }}">
            <div class="page-header-title">
                <div class="container">
                    <div class="row">
                        <div class="col-12 offset-xl-1 col-lg-11">
                            <a class="back-link fz-4" href="{{ route('news.index') }}">
                                Все {{ translate('novosti') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <section class="page-section">
            <div class="container">
                <div class="row">
                    <div class="col-12 offset-xl-1 col-md-2" data-aos="fade-right" data-aos-duration="700"
                         data-aos-once="true">
                        <time class="news-item-date">{{ $post->published_at_def }}</time>
                    </div>
                    <div class="col-12 col-xl-6 col-md-8" data-aos="fade-up" data-aos-duration="700"
                         data-aos-once="true">
                        <div class="single-news-content">
                            <h1 class="single-news-title fz-5">
                                {{ $post->title }}
                            </h1>
                            {!! $post->content !!}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection

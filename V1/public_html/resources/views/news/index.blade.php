@extends('layouts.app')

@section('title')
    {{ translate('novosti') }}
@endsection
@section('content')
    <main class="page">
        <div class="page-header" data-aos="fade-down" data-aos-duration="700" data-aos-once="true">
            <img src="{{ $setting->data['news_list_header'] ?? '/images/content/news-header-img.png' }}" alt="">
            <div class="page-header-title">
                <div class="container">
                    <div class="row">
                        <div class="col-12 offset-lg-1 col-lg-11">
                            <h1 data-aos-duration="700" data-aos-delay="700" data-aos-once="true">
                                {{ translate('novosti') }}
                            </h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <section class="page-section">
            <div class="container">
                <div class="row">
                    @foreach($posts as $post)
                        <div class="col-12 col-sm-6 col-lg-5 offset-lg-1 news-divider" data-aos="fade-up"
                             data-aos-duration="700" data-aos-once="true">
                            <div class="news-item">
                                <a href="{{ route('news.show', $post) }}" class="news-item-link"></a>
                                <div class="news-item-img">
                                    <img src="{{ $post->image_url }}">
                                </div>
                                <div class="news-item-info">
                                    <time class="news-item-date">
                                        {{ $post->published_at_def }}
                                    </time>
                                    <h3 class="news-item-title">
                                        {{ $post->title }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{ $posts->links('includes.pagination') }}
            </div>
        </section>
    </main>
@endsection

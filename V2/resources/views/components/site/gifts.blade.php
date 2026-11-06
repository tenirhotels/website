@props(['limit' => 3, 'link' => true])
@php
    $all = collect(\App\Support\Gifts::offers());
    $gifts = $all->take($limit);
    $link = $link && $all->count() > $gifts->count();
@endphp
@if ($gifts->isNotEmpty())
    <section class="bg-snow">
        <div class="container-site flex flex-col gap-8 md:gap-12 py-14 md:py-20 lg:py-24">
            <x-site.section-head :eyebrow="__('gifts.eyebrow')" :title="__('gifts.title')" :link="$link ? route('gifts') : null" :linkLabel="__('gifts.all')">
                <p class="lead">{{ __('gifts.lead') }}</p>
            </x-site.section-head>
            <div class="rail sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($gifts as $g)
                    @include('site.gifts.card', ['g' => $g])
                @endforeach
            </div>
        </div>
    </section>
@endif

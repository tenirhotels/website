@props(['hotel' => null, 'heading' => true, 'compact' => false])
@php
    // Отзывы гостей — виджет Exely (TravelLine) «reviews-widget»: отзывы приходят из Exely, вручную их вести не нужно.
    $context = config('tenir.exely.reviews_context');
    $reviewsUrl = $hotel ? \App\Support\Property::hotelUrl($hotel, 'reviews') : route('booking');
    $exelyLocale = \App\Support\Locales::all()[app()->getLocale()]['exely'] ?? 'ru';
@endphp
@if ($compact)
<section id="reviews" class="bg-white">
    <div class="container-site flex flex-col items-start gap-4 py-10 md:flex-row md:items-center md:justify-between md:py-12">
        <div id="tl-reputation-widget" class="min-h-[80px] w-full max-w-[340px]"></div>
        <a href="{{ $reviewsUrl }}" class="text-[13px] uppercase tracking-[0.2em] text-forest underline-offset-4 hover:underline">{{ __('site.reviews.all') }}</a>
    </div>
</section>
@else
<section id="reviews" class="bg-white">
    <div class="container-site py-14 md:py-20 lg:py-24">
        @if ($heading)<h2 class="h-section mb-8">{{ __('site.reviews.title') }}</h2>@endif
        <div id="tl-reviews-widget" class="min-h-[120px]"></div>
    </div>
</section>
@endif
<script>
    (function (w) {
        var q = [
            ['setContext', @json($context), @json($exelyLocale)],
            @if ($compact)
            ['embed', 'reputation-widget', { provider: @json((string) config('tenir.exely.provider')), container: 'tl-reputation-widget', theme: 'global', reviewsUrl: @json($reviewsUrl) }]
            @else
            ['embed', 'reviews-widget', { provider: @json((string) config('tenir.exely.provider')), container: 'tl-reviews-widget', theme: 'global', bookingUrl: @json(route('booking')) }]
            @endif
        ];
        var t = w.bookingengine = (w.bookingengine || {}), ti = t.integration = (t.integration || {});
        ti.__cq = ti.__cq ? ti.__cq.concat(q) : q;
        if (!ti.__loader) {
            ti.__loader = true;
            var d = w.document, s = d.createElement('script');
            s.async = true;
            s.src = @json(config('tenir.exely.reviews_loader'));
            (d.getElementsByTagName('head')[0] || d.getElementsByTagName('body')[0]).appendChild(s);
        }
    })(window);
</script>

@props(['hotel' => null])
@php
    // Отзывы гостей — виджет Exely (TravelLine) «reviews-widget»: отзывы приходят из Exely, вручную их вести не нужно.
    $context = $hotel?->exelyContext() ?? config('tenir.exely.context');
    $exelyLocale = \App\Support\Locales::all()[app()->getLocale()]['exely'] ?? 'ru';
@endphp
<section id="reviews" class="bg-white">
    <div class="container-site py-14 md:py-20 lg:py-24">
        <h2 class="h-section mb-8">{{ __('site.reviews.title') }}</h2>
        <div id="tl-reviews-widget" class="min-h-[120px]"></div>
    </div>
</section>
<script>
    (function (w) {
        var q = [
            ['setContext', @json($context), @json($exelyLocale)],
            ['embed', 'reviews-widget', { provider: @json((string) config('tenir.exely.provider')), container: 'tl-reviews-widget', theme: 'global', bookingUrl: @json(route('booking')) }]
        ];
        var t = w.travelline = (w.travelline || {}), ti = t.integration = (t.integration || {});
        ti.__cq = ti.__cq ? ti.__cq.concat(q) : q;
        if (!ti.__loader) {
            ti.__loader = true;
            var d = w.document, s = d.createElement('script');
            s.async = true;
            s.src = @json(config('tenir.exely.loader'));
            (d.getElementsByTagName('head')[0] || d.getElementsByTagName('body')[0]).appendChild(s);
        }
    })(window);
</script>

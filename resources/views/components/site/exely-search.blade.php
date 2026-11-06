@props(['hotel' => null])
@php
    // Загрузчик виджета Exely «search-form» (контейнер #tl-search-form: плашка под шапкой или форма на первом экране).
    $context = $hotel?->exelyContext() ?? config('tenir.exely.context');
    $exelyLocale = \App\Support\Locales::all()[app()->getLocale()]['exely'] ?? 'ru';
@endphp
<script>
    (function (w) {
        var q = [
            ['setContext', @json($context), @json($exelyLocale)],
            ['embed', 'search-form', { container: 'tl-search-form' }]
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

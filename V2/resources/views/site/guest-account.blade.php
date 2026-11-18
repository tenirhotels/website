<x-layouts.site :seo="$seo" :hotel="$hotel">
    <x-slot:head>
        {{-- Личный кабинет гостя — модуль Exely «guest-account» (прежний загрузчик, как у виджета отзывов) --}}
        <script>
            (function (w) {
                var q = [
                    ['setContext', @json(config('tenir.exely.reviews_context')), @json($exelyLocale)],
                    ['embed', 'guest-account', {
                        provider: @json((string) config('tenir.exely.provider')),
                        container: 'tl-guest-account',
                        theme: 'express-bluegrey',
                        gpPagePath: @json(parse_url(route('guest-account'), PHP_URL_PATH)),
                        bfPagePath: @json(parse_url(route('booking'), PHP_URL_PATH))
                    }]
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
    </x-slot:head>

    <div class="bg-exely-account">
        <div class="container-site pb-10 pt-6 md:pt-8">
            <div class="mb-4 flex flex-wrap items-end justify-between gap-x-8 gap-y-1">
                <h1 class="h-display mt-1 text-3xl text-forest md:text-4xl">{{ __('site.booking.account') }}</h1>
                <a href="{{ route('booking') }}" class="link-line">{{ __('site.booking.title') }}</a>
            </div>

            <div id="tl-guest-account" class="min-h-[420px]">
                <p class="py-10 md:py-14 text-center text-exely-ink">{{ __('site.booking.loading') }}</p>
            </div>
            <noscript><p class="py-10 text-center">{{ __('site.booking.noscript') }}</p></noscript>
        </div>
    </div>
</x-layouts.site>

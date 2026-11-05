@php use App\Models\Setting; @endphp
<x-layouts.site :seo="$seo" :hotel="$hotel">
    <x-slot:head>
        {{-- Модуль бронирования TravelLine / Exely (как в V1: booking-form) --}}
        <script>
            (function (w) {
                var q = [
                    ['setContext', @json($context), @json($exelyLocale)],
                    ['embed', 'booking-form', { container: 'tl-booking-form' }]
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
    </x-slot:head>

    <section class="bg-forest text-snow">
        <div class="container-site flex flex-col gap-4 py-14">
            <p class="eyebrow-light">{{ $hotel?->t('name') }}</p>
            <h1 class="h-display text-5xl md:text-6xl">{{ __('site.booking.title') }}</h1>
            <p class="max-w-xl text-snow/85">{{ __('site.booking.intro') }}</p>
        </div>
    </section>

    <section class="container-site py-12">
        <div id="tl-booking-form" class="min-h-[480px]">
            <p class="py-20 text-center text-stone">{{ __('site.booking.loading') }}</p>
        </div>
        <noscript><p class="py-10 text-center">{{ __('site.booking.noscript') }}</p></noscript>

        <div class="mt-12 flex flex-wrap items-center gap-6 border-t border-mist pt-8 text-[15px] text-slate">
            <span>{{ __('site.booking.help') }}</span>
            @if ($phone = $hotel?->phone ?? Setting::get('contacts.phone'))
                <a href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}" class="hover:text-ember">{{ $phone }}</a>
            @endif
            @if ($wa = $hotel?->whatsapp ?? Setting::get('contacts.whatsapp'))
                <a href="https://wa.me/{{ preg_replace('/\D/', '', $wa) }}" class="hover:text-ember">WhatsApp</a>
            @endif
        </div>
    </section>
</x-layouts.site>

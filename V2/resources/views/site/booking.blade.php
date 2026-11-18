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

    {{-- Фон и цвета страницы совпадают с модулем Exely, чтобы он не выделялся. --}}
    <div class="bg-exely">
        <div class="container-site pb-10 pt-6 md:pt-8">
            <div class="mb-4 flex flex-wrap items-end justify-between gap-x-8 gap-y-1">
                <div>
                    <p class="eyebrow">{{ $hotel?->t('name') }}</p>
                    <h1 class="h-display mt-1 text-3xl text-forest md:text-4xl">{{ __('site.booking.title') }}</h1>
                </div>
                <p class="max-w-md text-[15px] text-exely-ink">{{ __('site.booking.intro') }}</p>
            </div>

            <div id="tl-booking-form" class="min-h-[420px]">
                <p class="py-10 md:py-14 text-center text-exely-ink">{{ __('site.booking.loading') }}</p>
            </div>
            <noscript><p class="py-10 text-center">{{ __('site.booking.noscript') }}</p></noscript>

            <div class="mt-8 flex flex-col gap-5 border-t border-exely-line pt-6 text-exely-ink md:flex-row md:items-end md:justify-between">
                <div class="flex flex-col gap-2">
                    <p class="text-[17px] text-ink">{{ __('site.booking.help') }}</p>
                    <div class="flex flex-wrap gap-x-6 gap-y-1 text-[17px]">
                        @if ($phone = $hotel?->phone ?? Setting::get('contacts.phone'))
                            <a href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}" class="hover:text-ember">{{ $phone }}</a>
                        @endif
                        @if ($wa = $hotel?->whatsapp ?? Setting::get('contacts.whatsapp'))
                            <a href="{{ wa_url($wa, 'stay', ['hotel' => $hotel?->t('name') ?: 'Tenir Hotels']) }}" class="hover:text-ember">WhatsApp</a>
                        @endif
                    </div>
                </div>
                <a href="{{ route('guest-account') }}" class="link-line self-start md:self-auto">{{ __('site.booking.account') }}</a>
            </div>
        </div>
    </div>
</x-layouts.site>

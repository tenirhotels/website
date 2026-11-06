<x-layouts.site :seo="$seo" :hotel="$experience->hotel">
    <x-site.page-hero :image="$experience->image" :eyebrow="collect([$experience->categoryLabel(), $experience->hotel?->t('name')])->filter()->implode(' · ')" :title="$experience->t('title')" :text="$experience->t('summary')" />

    <section class="container-site flex flex-col gap-8 md:gap-14 py-12 md:py-16 lg:py-20 lg:flex-row lg:gap-20">
        <div class="flex flex-[1.5] flex-col gap-10">
            <div class="prose-tenir">{!! $experience->t('description') !!}</div>
            @if ($experience->gallery)
                <div class="grid grid-cols-2 gap-3">
                    @foreach ($experience->gallery as $img)
                        <div class="aspect-[4/3] overflow-hidden rounded-2xl"><x-site.media :src="$img" :alt="$experience->t('title')" /></div>
                    @endforeach
                </div>
            @endif
        </div>
        <aside class="flex-1">
            <dl class="flex flex-col gap-5 bg-white p-8 rounded-3xl text-[15px]">
                @if ($experience->seasonLabels())
                    <div><dt class="field-label mb-1">{{ __('site.common.seasons') }}</dt><dd>{{ $experience->seasonLabels() }}</dd></div>
                @endif
                @if ($experience->t('duration'))
                    <div><dt class="field-label mb-1">{{ __('site.common.duration') }}</dt><dd>{{ $experience->t('duration') }}</dd></div>
                @endif
                @if ($experience->price_from)
                    <div><dt class="sr-only">{{ __('site.hotel.from') }}</dt><dd class="text-2xl">{{ __('site.common.price_from', ['price' => price($experience->price_from)]) }}</dd></div>
                @endif
                @php
                    $digits = fn ($v) => preg_replace('/[^\d+]/', '', (string) $v);
                    $cta = $experience->cta ?: 'booking';
                    $salesPhone = \App\Models\Setting::get('contacts.sales_phone');
                    $salesEmail = \App\Models\Setting::get('contacts.sales_email');
                    $phone = $experience->hotel?->phone ?? \App\Models\Setting::get('contacts.phone');
                    $wa = $experience->hotel?->whatsapp ?? \App\Models\Setting::get('contacts.whatsapp');
                    $email = \App\Models\Setting::get('contacts.email');
                    if ($cta === 'sales' && ! $salesPhone && ! $salesEmail) { $cta = 'contact'; }
                    $subject = rawurlencode($experience->t('title'));
                @endphp
                @if ($cta === 'sales')
                    <div class="mt-2 border-t border-mist pt-5">
                        <dt class="field-label mb-1">{{ __('site.cta.request_label') }}</dt>
                        <dd class="text-stone">{{ __('site.cta.request_text') }}</dd>
                    </div>
                    @if ($salesEmail)<a href="mailto:{{ $salesEmail }}?subject={{ $subject }}" class="btn-primary">{{ __('site.cta.send_request') }}</a>@endif
                    <div class="flex flex-col gap-1">
                        @if ($salesPhone)<a href="tel:{{ $digits($salesPhone) }}" class="hover:text-ember">{{ $salesPhone }}</a>@endif
                        @if ($salesEmail)<a href="mailto:{{ $salesEmail }}?subject={{ $subject }}" class="hover:text-ember">{{ $salesEmail }}</a>@endif
                    </div>
                @elseif ($cta === 'contact')
                    <div class="mt-2 border-t border-mist pt-5">
                        <dt class="field-label mb-1">{{ __('site.cta.ask_label') }}</dt>
                        <dd class="text-stone">{{ __('site.cta.ask_text') }}</dd>
                    </div>
                    @if ($phone)<a href="tel:{{ $digits($phone) }}" class="btn-primary">{{ __('site.cta.call') }} · {{ $phone }}</a>@endif
                    <div class="flex flex-col gap-1">
                        @if ($wa)<a href="https://wa.me/{{ preg_replace('/\D/', '', $wa) }}" class="hover:text-ember">WhatsApp</a>@endif
                        @if ($email)<a href="mailto:{{ $email }}?subject={{ $subject }}" class="hover:text-ember">{{ $email }}</a>@endif
                    </div>
                @else
                    <a href="{{ route('booking', array_filter(['hotel' => $experience->hotel?->slug])) }}" class="btn-primary mt-2">{{ __('site.common.book_now') }}</a>
                @endif
            </dl>
        </aside>
    </section>

    @if ($related->isNotEmpty())
        <section class="bg-sand">
            <div class="container-site flex flex-col gap-8 md:gap-12 py-12 md:py-16 lg:py-20">
                <x-site.section-head :title="__('site.common.related')" />
                <div class="grid gap-8 md:grid-cols-3">
                    @foreach ($related as $e)
                        <x-site.card :href="$e->url()" :image="$e->image" :title="$e->t('title')" :text="$e->t('summary')" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-layouts.site>

<x-layouts.site :seo="$seo" :hotel="$hotel">
    <x-site.page-hero :image="$hotel->gallery[0] ?? $hotel->hero_image" :eyebrow="$hotel->t('name')" :title="__('site.hotel.getting_here')" :text="$hotel->t('address')" />
    <x-site.hotel-nav :hotel="$hotel" />

    @php
        $showAccess = $hotel->hasCable() && $hotel->hasMap();
        $others = $showAccess ? $hotel->transports->whereNotIn('kind', ['cable_line', 'guest_ride']) : $hotel->transports;
    @endphp
    @if ($showAccess)
        <x-site.access :hotel="$hotel" />
    @endif

    @if ($others->isNotEmpty())
        <section class="container-site pt-10 md:pt-14">
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($others as $t)
                    <article class="flex flex-col gap-3 overflow-hidden rounded-3xl border border-mist p-6">
                        @if ($t->image)<div class="-mx-6 -mt-6 aspect-[16/9]"><x-site.media :src="$t->image" :alt="$t->t('name')" /></div>@endif
                        <h3 class="font-display text-xl">{{ $t->t('name') }}</h3>
                        @if ($t->t('summary'))<p class="text-base leading-relaxed text-slate">{{ $t->t('summary') }}</p>@endif
                        @if ($t->hours)<p class="font-display text-lg text-ink">{{ $t->hours }}</p>@endif
                        @if ($t->t('price'))<p class="font-display text-2xl text-ember">{{ $t->t('price') }}</p>@endif
                        @if ($t->t('note'))<p class="text-sm text-stone">{{ $t->t('note') }}</p>@endif
                        @php
                            $stops = collect($t->map_pins ?? [])->map(fn ($id) => $hotel->resortMap()?->pin($id))->filter();
                        @endphp
                        @if ($stops->isNotEmpty())
                            <ul class="flex flex-col gap-1 text-[15px]">
                                @foreach ($stops as $stop)
                                    <li><a href="{{ route('map') }}#point-{{ $stop['id'] }}" class="link-line">{{ __('access.on_map') }}: {{ $stop['label'] }}</a></li>
                                @endforeach
                            </ul>
                        @endif
                        @if ($t->orderable)
                            @php
                                $waDigits = preg_replace('/\D/', '', (string) $hotel->whatsapp);
                                $msg = __('access.order_msg', ['name' => $t->t('name') ?: ($t::KINDS[$t->kind] ?? ''), 'hotel' => $hotel->t('name')]);
                            @endphp
                            <div class="mt-auto flex flex-wrap items-center gap-x-5 gap-y-2 pt-2">
                                @if ($waDigits)
                                    <a href="https://wa.me/{{ $waDigits }}?text={{ rawurlencode($msg) }}" class="btn-primary" target="_blank" rel="noopener">{{ __('access.order') }}</a>
                                @elseif ($hotel->phone)
                                    <a href="tel:{{ preg_replace('/[^\d+]/', '', $hotel->phone) }}" class="btn-primary">{{ __('access.order') }}</a>
                                @elseif ($hotel->email)
                                    <a href="mailto:{{ $hotel->email }}?subject={{ rawurlencode($t->t('name') ?: __('access.order')) }}&body={{ rawurlencode($msg) }}" class="btn-primary">{{ __('access.order') }}</a>
                                @else
                                    <a href="{{ route('contact') }}" class="btn-primary">{{ __('access.order') }}</a>
                                @endif
                                @if ($waDigits && $hotel->phone)<a href="tel:{{ preg_replace('/[^\d+]/', '', $hotel->phone) }}" class="link-line text-[15px]">{{ __('access.order_call') }}</a>@endif
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <section class="container-site flex flex-col gap-8 md:gap-14 pb-12 pt-0 md:py-16 lg:py-20 lg:flex-row lg:gap-20">
        <div class="prose-tenir flex-[1.5] [&>*:first-child]:mt-0">{!! $hotel->t('getting_here') !!}</div>
        <aside class="flex flex-1 flex-col gap-3 text-base text-slate">
            @if ($hotel->hasMap())<a href="{{ route('map') }}" class="btn-primary self-start">{{ __('map.open_map') }}</a>@endif
            @if ($hotel->latitude)
                <a href="https://www.google.com/maps/search/?api=1&query={{ $hotel->latitude }},{{ $hotel->longitude }}" class="btn-dark self-start" target="_blank" rel="noopener">Google Maps</a>
                <a href="https://2gis.kz/geo/{{ $hotel->longitude }},{{ $hotel->latitude }}" class="link-line mt-2 self-start" target="_blank" rel="noopener">2GIS</a>
            @endif
            @if ($hotel->phone)<a href="tel:{{ preg_replace('/[^\d+]/', '', $hotel->phone) }}" class="mt-6 hover:text-ember">{{ $hotel->phone }}</a>@endif
            @if ($hotel->whatsapp)<a href="https://wa.me/{{ preg_replace('/\D/', '', $hotel->whatsapp) }}" class="hover:text-ember">WhatsApp</a>@endif
        </aside>
    </section>
</x-layouts.site>

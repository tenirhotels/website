<x-layouts.site :seo="$seo" :hotel="$hotel">
    <x-site.page-hero :image="$hotel->gettingImage()" :eyebrow="$hotel->t('name')" :title="__('site.hotel.getting_here')" :text="$hotel->t('address')" />
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
                    @php
                        $ride = $t->isRide();
                        $from = trim((string) $t->t('route_from'));
                        $to = trim((string) $t->t('route_to'));
                        $stops = collect($t->map_pins ?? [])->map(fn ($id) => $hotel->resortMap()?->pin($id))->filter();
                        $waDigits = preg_replace('/\D/', '', (string) $hotel->whatsapp);
                        $msg = __('access.order_msg', ['name' => $t->title(), 'hotel' => $hotel->t('name')]);
                    @endphp
                    <article class="flex flex-col overflow-hidden rounded-3xl border border-mist bg-white">
                        @if ($t->image)<div class="aspect-[16/9]"><x-site.media :src="$t->image" :alt="$t->title()" round="" /></div>@endif
                        @if ($ride)
                            <p class="flex items-center gap-2.5 bg-sand px-5 py-3.5 text-[13px] font-semibold uppercase tracking-[0.18em] text-forest md:px-6">
                                <span class="flex h-6 w-6 items-center justify-center text-ember">{{ site_icon($t->kind === 'taxi' ? 'car' : 'bus', 'h-5 w-5') }}</span>
                                {{ __('access.kind_'.$t->kind) }}
                            </p>
                        @endif
                        <div class="flex flex-1 flex-col gap-3 p-5 md:p-6">
                        @if (! ($ride && $from !== '' && $to !== '' && trim((string) $t->t('name')) === ''))
                            <h3 class="font-display text-xl">{{ $t->title() }}</h3>
                        @endif
                        @if ($ride && $from !== '' && $to !== '')
                            <div class="flex gap-3" aria-label="{{ $from }} — {{ $to }}">
                                <span class="flex shrink-0 flex-col items-center pt-1" aria-hidden="true">
                                    <span class="size-3.5 shrink-0 rounded-full border-2 border-forest bg-snow"></span>
                                    <span class="my-1 w-0.5 flex-1 bg-ember/40"></span>
                                    <span class="size-3.5 shrink-0 rounded-full bg-ember"></span>
                                </span>
                                <div class="flex flex-col gap-3">
                                    <p class="flex flex-col"><span class="text-xs uppercase tracking-[0.14em] text-stone">{{ __('access.route_from') }}</span><span class="text-lg leading-snug text-ink">{{ $from }}</span></p>
                                    <p class="flex flex-col"><span class="text-xs uppercase tracking-[0.14em] text-stone">{{ __('access.route_to') }}</span><span class="text-lg leading-snug text-ink">{{ $to }}</span></p>
                                </div>
                            </div>
                        @endif
                        @if ($t->t('summary'))<p class="text-base leading-relaxed text-slate">{{ $t->t('summary') }}</p>@endif
                        @if ($t->t('price'))
                            <div class="flex items-baseline justify-between gap-3 rounded-2xl bg-ember/10 px-4 py-3">
                                <span class="text-sm text-stone">{{ __('access.price') }}</span>
                                <span class="font-display text-2xl text-ember">{{ $t->t('price') }}</span>
                            </div>
                        @endif
                        @if ($t->hours)<p class="font-display text-lg text-ink">{{ $t->hours }}</p>@endif
                        @if ($t->t('note'))<p class="text-sm text-stone">{{ $t->t('note') }}</p>@endif
                        @if ($stops->isNotEmpty())
                            <ul class="flex flex-col gap-1 text-[15px]">
                                @foreach ($stops as $stop)
                                    <li><a href="{{ route('map') }}#point-{{ $stop['id'] }}" class="link-line">{{ __('access.on_map') }}: {{ $stop['label'] }}</a></li>
                                @endforeach
                            </ul>
                        @endif
                        @if ($t->orderable)
                            <div class="mt-auto flex flex-wrap items-center gap-x-5 gap-y-2 pt-2">
                                @if ($waDigits)
                                    <a href="https://wa.me/{{ $waDigits }}?text={{ rawurlencode($msg) }}" class="btn-primary" target="_blank" rel="noopener">{{ __('access.order') }}</a>
                                @elseif ($hotel->phone)
                                    <a href="tel:{{ preg_replace('/[^\d+]/', '', $hotel->phone) }}" class="btn-primary">{{ __('access.order') }}</a>
                                @elseif ($hotel->email)
                                    <a href="mailto:{{ $hotel->email }}?subject={{ rawurlencode($t->title()) }}&body={{ rawurlencode($msg) }}" class="btn-primary">{{ __('access.order') }}</a>
                                @else
                                    <a href="{{ route('contact') }}" class="btn-primary">{{ __('access.order') }}</a>
                                @endif
                                @if ($waDigits && $hotel->phone)<a href="tel:{{ preg_replace('/[^\d+]/', '', $hotel->phone) }}" class="link-line text-[15px]">{{ __('access.order_call') }}</a>@endif
                            </div>
                        @endif
                    </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <section class="container-site flex flex-col gap-8 pb-12 pt-8 md:py-16 lg:py-20">
        @if ($hotel->t('getting_here'))<div class="prose-tenir [&>*:first-child]:mt-0">{!! $hotel->t('getting_here') !!}</div>@endif
        <a href="{{ $hotel->sectionUrl('before-you-arrive') }}" class="group flex flex-col gap-2.5 rounded-3xl border border-mist bg-white p-6 transition hover:border-ember md:p-8">
            <span class="flex items-center justify-between gap-4 font-display text-xl md:text-2xl">{{ __('site.hotel.before_arrive') }}<svg class="size-5 shrink-0 text-ember transition-transform group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg></span>
            <span class="text-base leading-relaxed text-slate">{{ $hotel->t('before_tease') ?: __('site.hotel.before_tease') }}</span>
        </a>
    </section>
</x-layouts.site>

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
                    <article class="flex flex-col gap-3 overflow-hidden rounded-3xl border border-mist p-6">
                        @if ($t->image)<div class="-mx-6 -mt-6 aspect-[16/9]"><x-site.media :src="$t->image" :alt="$t->title()" /></div>@endif
                        @if ($ride)
                            <p class="flex items-center gap-2 text-[13px] uppercase tracking-[0.14em] text-stone">
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-sand text-ember">{{ site_icon($t->kind === 'taxi' ? 'car' : 'bus', 'h-5 w-5') }}</span>
                                {{ __('access.kind_'.$t->kind) }}
                            </p>
                        @endif
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
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <section class="container-site flex flex-col gap-8 pb-12 pt-8 md:py-16 lg:py-20">
        @if ($hotel->t('getting_here'))<div class="prose-tenir [&>*:first-child]:mt-0">{!! $hotel->t('getting_here') !!}</div>@endif
        <a href="{{ $hotel->sectionUrl('before-you-arrive') }}" class="group flex flex-col gap-2 rounded-3xl border border-mist bg-sand p-6 transition hover:border-ember md:p-8">
            <span class="font-display text-xl md:text-2xl">{{ __('site.hotel.before_arrive') }}</span>
            <span class="text-base leading-relaxed text-slate">{{ $hotel->t('before_tease') ?: __('site.hotel.before_tease') }}</span>
            <span class="link-line mt-2 self-start">{{ __('site.hotel.before_more') }}</span>
        </a>
    </section>
</x-layouts.site>

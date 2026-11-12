<article class="group flex h-full flex-col overflow-hidden rounded-2xl border border-mist bg-white">
    @php $tags = collect([$room->size_m2 ? $room->size_m2.' м²' : null, $room->max_guests ? __('site.hotel.up_to_guests', ['count' => $room->max_guests]) : null, $room->t('view')])->filter(); @endphp
    <a href="{{ $room->url() }}" class="relative block aspect-[4/3] shrink-0 overflow-hidden">
        <x-site.media :src="$room->image" alt="" class="transition-transform duration-700 group-hover:scale-[1.03]" />
        <span class="pointer-events-none absolute inset-0 bg-gradient-to-t from-forest-deep/80 via-forest-deep/20 to-transparent" aria-hidden="true"></span>
        @if ($tags->isNotEmpty())
            <ul class="absolute left-4 right-4 top-4 flex flex-wrap gap-1.5">
                @foreach ($tags as $tag)
                    <li class="rounded-full bg-white/90 px-3 py-1.5 text-[12px] leading-none text-ink backdrop-blur-sm">{{ $tag }}</li>
                @endforeach
            </ul>
        @endif
        <h3 class="absolute inset-x-5 bottom-4 flex min-h-[3.1rem] items-start justify-between gap-3 font-display text-[24px] leading-[1.15] text-white">
            <span class="line-clamp-2">{{ $room->t('name') }}</span>
            <svg class="mt-1 size-5 shrink-0 transition-transform group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg>
        </h3>
    </a>
    <div class="flex flex-1 flex-col gap-3 p-5">
    <p class="line-clamp-4 text-[15px] leading-relaxed text-slate">{{ $room->t('summary') }}</p>
    <div class="mt-auto flex flex-wrap items-center justify-between gap-4 pt-2">
        @if ($room->price_from)
            <span class="text-[15px] text-slate">{{ __('site.hotel.from') }} <strong class="text-lg font-normal text-ink">{{ price($room->price_from) }} ₸</strong></span>
        @endif
        <a href="{{ $room->bookingUrl() }}" class="text-[13px] uppercase tracking-[0.2em] text-ember hover:text-forest inline-block border-b border-current pb-1.5">{{ __('site.hotel.check_prices') }} →</a>
    </div>
    </div>
</article>

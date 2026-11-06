<article class="group flex flex-col gap-4">
    <a href="{{ $room->url() }}" class="block aspect-[4/3] overflow-hidden rounded-2xl" tabindex="-1" aria-hidden="true">
        <x-site.media :src="$room->image" :alt="$room->t('name')" class="transition-transform duration-700 group-hover:scale-[1.03]" />
    </a>
    <h3 class="font-display text-[28px] leading-tight"><a href="{{ $room->url() }}" class="hover:text-ember">{{ $room->t('name') }}</a></h3>
    <p class="text-sm text-stone">
        {{ collect([$room->size_m2 ? $room->size_m2.' м²' : null, $room->max_guests ? __('site.hotel.up_to_guests', ['count' => $room->max_guests]) : null, $room->t('view')])->filter()->implode(' · ') }}
    </p>
    <p class="leading-relaxed text-slate">{{ $room->t('summary') }}</p>
    <div class="mt-auto flex flex-wrap items-center justify-between gap-4 pt-2">
        @if ($room->price_from)
            <span class="text-sm text-slate">{{ __('site.hotel.from') }} <strong class="text-lg font-normal text-ink">{{ price($room->price_from) }} ₸</strong></span>
        @endif
        <a href="{{ $room->bookingUrl() }}" class="text-[13px] uppercase tracking-[0.2em] text-ember hover:text-forest">{{ __('site.hotel.check_prices') }} →</a>
    </div>
</article>

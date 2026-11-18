{{-- Карточка события: фото (своё, иначе фото отеля, иначе тёмная панель с петроглифами) и плашка с датой --}}
@php
    $photo = $event->image ?: $event->hotel?->seasonHero();
@endphp
<a href="{{ $event->url() }}" class="group flex h-full flex-col overflow-hidden rounded-2xl border border-mist bg-white">
    <div class="relative aspect-[3/2] shrink-0 overflow-hidden bg-forest-deep">
        @if ($photo)
            <x-site.media :src="$photo" :alt="''" round="" class="transition-transform duration-700 group-hover:scale-[1.03]" />
            <span class="pointer-events-none absolute inset-0 bg-[linear-gradient(to_top,rgb(22_33_27/.55)_0%,rgb(22_33_27/0)_55%)]" aria-hidden="true"></span>
        @else
            <div class="pattern-tile bg-white/[0.07]" aria-hidden="true"></div>
        @endif
        <div class="absolute left-4 top-4 min-w-[84px] rounded-xl bg-white px-4 py-3 text-center shadow-sm">
            <p class="font-display text-[30px] leading-none text-ember">{{ $event->dayBadge() }}</p>
            <p class="mt-1.5 text-[11px] uppercase tracking-[0.18em] text-stone">{{ $event->monthBadge() }}</p>
        </div>
    </div>
    <div class="flex flex-1 flex-col gap-3 p-5 md:p-6">
        <h3 class="font-display text-[24px] leading-tight transition-colors group-hover:text-ember">{{ $event->t('title') }}</h3>
        @if ($event->t('summary'))<p class="line-clamp-3 text-[15px] leading-relaxed text-slate">{{ $event->t('summary') }}</p>@endif
        <div class="mt-auto flex flex-col gap-1.5 border-t border-mist pt-4 text-[12px] uppercase tracking-[0.16em] text-stone">
            @if ($event->isRecurring())
                <p class="flex items-center gap-2"><svg class="size-4 shrink-0 text-ember" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="5" width="16" height="15" rx="2"/><path d="M8 3v4M16 3v4M4 10h16"/></svg>{{ $event->daysLabel() }}</p>
            @endif
            @if ($event->time_text)
                <p class="flex items-center gap-2"><svg class="size-4 shrink-0 text-ember" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>{{ $event->time_text }}</p>
            @endif
            <p class="flex items-start gap-2"><svg class="mt-px size-4 shrink-0 text-ember" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s-7-6.2-7-11a7 7 0 0114 0c0 4.8-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>{{ collect([$event->t('place'), $event->hotel?->t('name') ?? __('site.events.whole_resort')])->filter()->unique()->implode(' · ') }}</p>
        </div>
    </div>
</a>

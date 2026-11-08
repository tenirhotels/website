{{-- Строка афиши: дата слева, название и место справа --}}
<a href="{{ $event->url() }}" class="group grid gap-4 border-t border-mist py-7 md:grid-cols-[200px_1fr_auto] md:items-baseline md:gap-10">
    <div>
        <p class="font-display text-3xl leading-none text-ember">{{ $event->dateLabel() }}</p>
        @if ($event->time_text)<p class="mt-2 text-[13px] uppercase tracking-[0.16em] text-stone">{{ $event->time_text }}</p>@endif
    </div>
    <div>
        <h3 class="font-display text-[24px] leading-tight group-hover:text-ember">{{ $event->t('title') }}</h3>
        @if ($event->t('summary'))<p class="mt-2 max-w-2xl leading-relaxed text-slate">{{ $event->t('summary') }}</p>@endif
        <p class="mt-2 text-xs uppercase tracking-[0.2em] text-stone">
            {{ collect([$event->t('place'), $event->hotel?->t('name') ?? __('site.events.whole_resort')])->filter()->unique()->implode(' · ') }}
        </p>
    </div>
    <span class="hidden text-[13px] uppercase tracking-[0.2em] group-hover:text-ember md:block">{{ __('site.common.learn_more') }} →</span>
</a>

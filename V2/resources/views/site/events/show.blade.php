<x-layouts.site :seo="$seo" :hotel="$event->hotel">
    <x-site.page-hero :image="$event->image" :eyebrow="collect([$event->whenLabel(), $event->hotel?->t('name') ?? __('site.events.whole_resort')])->filter()->implode(' · ')" :title="$event->t('title')" :text="$event->t('summary')" />

    <section class="container-site flex flex-col gap-8 md:gap-14 py-12 md:py-16 lg:py-20 lg:flex-row lg:gap-20">
        <div class="flex flex-[1.5] flex-col gap-10">
            @if ($event->isPast())
                <p class="rounded-2xl bg-sand p-5 text-slate">{{ __('site.events.past') }}</p>
            @endif
            @if ($event->t('description'))
                <div class="prose-tenir">{!! $event->t('description') !!}</div>
            @endif
        </div>
        <aside class="flex-1">
            <dl class="flex flex-col gap-5 rounded-3xl bg-white p-8 text-base">
                <div><dt class="field-label mb-1">{{ __('site.events.when') }}</dt><dd>{{ $event->dateLabel() }}@if ($event->time_text), {{ $event->time_text }}@endif</dd></div>
                @if ($event->t('place') || $event->hotel)
                    <div><dt class="field-label mb-1">{{ __('site.events.where') }}</dt><dd>{{ collect([$event->t('place'), $event->hotel?->t('name') ?? __('site.events.whole_resort')])->filter()->unique()->implode(' · ') }}</dd></div>
                @endif
                @unless ($event->isPast())
                    <x-site.cta-box :cta="$event->cta" :title="$event->t('title')" :hotel="$event->hotel" />
                @endunless
            </dl>
        </aside>
    </section>

    @if ($others->isNotEmpty())
        <section class="bg-sand">
            <div class="container-site py-12 md:py-16">
                <x-site.section-head :title="__('site.events.also')" :link="route('events.index')" :link-label="__('site.common.view_all')" />
                <div class="mt-6 border-b border-mist">
                    @foreach ($others as $o)@include('site.events.partials.row', ['event' => $o])@endforeach
                </div>
            </div>
        </section>
    @endif
</x-layouts.site>

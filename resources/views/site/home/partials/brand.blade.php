{{-- Философия бренда и Sustainability — данные со страниц about и sustainability (редактируются в админке) --}}
@php
    $word = collect($brandPage?->blocks() ?? [])->firstWhere('type', 'word')['data'] ?? null;
    $pillars = collect($sustainabilityPage?->blocks() ?? [])->firstWhere('type', 'pillars')['data'] ?? null;
@endphp

@if ($brandPage)
    <section class="bg-forest text-snow">
        <div class="container-site flex max-w-4xl flex-col items-center gap-7 py-28 text-center md:py-36">
            <p class="eyebrow-light">{{ $brandPage->t('title') }}</p>
            <p class="font-display text-7xl tracking-[0.04em] md:text-[120px] md:leading-none">{{ $word['word'] ?? 'Тәңір' }}</p>
            @if (! empty($word['meaning']))<p class="text-sm uppercase tracking-[0.3em] text-snow/80">{{ $word['meaning'] }}</p>@endif
            @if (! empty($word['text']))<p class="font-display text-2xl italic leading-relaxed md:text-[34px]">{{ $word['text'] }}</p>@endif
            <a href="{{ $brandPage->url() }}" class="link-line text-snow">{{ __('site.common.learn_more') }}</a>
        </div>
    </section>
@endif

@if ($sustainabilityPage && $pillars)
    <section class="container-site flex flex-col gap-12 py-24">
        <x-site.section-head :eyebrow="$sustainabilityPage->t('title')" :title="$pillars['heading'] ?? $sustainabilityPage->t('summary')" :link="$sustainabilityPage->url()" :link-label="__('site.common.learn_more')" />
        <ol class="grid border-t border-mist sm:grid-cols-2 lg:grid-cols-4">
            @foreach (array_slice($pillars['items'] ?? [], 0, 4) as $i => $item)
                <li class="flex flex-col gap-3 border-mist py-8 sm:pr-8 lg:border-l lg:px-8 lg:first:border-l-0 lg:first:pl-0">
                    <span class="font-display text-4xl text-ember">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    <h3 class="text-lg uppercase tracking-[0.06em]">{{ $item['title'] ?? '' }}</h3>
                    @if (! empty($item['text']))<p class="leading-relaxed text-slate">{{ $item['text'] }}</p>@endif
                </li>
            @endforeach
        </ol>
    </section>
@endif

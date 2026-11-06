@props(['blocks' => []])
{{-- Рендер блоков страницы бренда (Filament Builder). --}}
@foreach ($blocks as $block)
    @php($d = $block['data'] ?? [])
    @switch($block['type'] ?? null)
        @case('intro')
            <section class="container-site flex max-w-4xl flex-col gap-6 py-20 text-center md:py-28">
                @if (! empty($d['eyebrow']))<p class="eyebrow">{{ $d['eyebrow'] }}</p>@endif
                <h2 class="h-section">{{ $d['heading'] ?? '' }}</h2>
                @if (! empty($d['text']))<p class="lead mx-auto max-w-2xl">{{ $d['text'] }}</p>@endif
            </section>
            @break

        @case('text')
            <section class="container-site max-w-3xl py-12 md:py-16">
                @if (! empty($d['heading']))<h2 class="h-section mb-8">{{ $d['heading'] }}</h2>@endif
                <div class="prose-tenir">{!! $d['body'] ?? '' !!}</div>
            </section>
            @break

        @case('image_text')
            <section class="container-site py-16 md:py-24">
                <div @class(['flex flex-col items-center gap-12 lg:flex-row lg:gap-20', 'lg:flex-row-reverse' => ($d['image_side'] ?? 'right') === 'left'])>
                    <div class="flex flex-1 flex-col gap-6">
                        @if (! empty($d['eyebrow']))<p class="eyebrow">{{ $d['eyebrow'] }}</p>@endif
                        @if (! empty($d['heading']))<h2 class="h-section">{{ $d['heading'] }}</h2>@endif
                        <div class="prose-tenir">{!! $d['body'] ?? '' !!}</div>
                    </div>
                    <div class="aspect-[4/5] w-full flex-1 lg:max-w-[560px]">
                        <x-site.media :src="$d['image'] ?? null" :alt="$d['heading'] ?? ''" />
                    </div>
                </div>
            </section>
            @break

        @case('quote')
            <section class="bg-sand">
                <figure class="container-site flex max-w-4xl flex-col items-center gap-6 py-20 text-center md:py-28">
                    <blockquote class="font-display text-3xl italic leading-snug md:text-4xl">«{{ $d['quote'] ?? '' }}»</blockquote>
                    @if (! empty($d['caption']))<figcaption class="text-sm uppercase tracking-[0.2em] text-stone">{{ $d['caption'] }}</figcaption>@endif
                </figure>
            </section>
            @break

        @case('word')
            <section class="bg-forest text-snow">
                <div class="container-site flex max-w-4xl flex-col items-center gap-6 py-24 text-center md:py-32">
                    <p class="font-display text-7xl tracking-[0.04em] md:text-9xl">{{ $d['word'] ?? 'Тәңір' }}</p>
                    @if (! empty($d['meaning']))<p class="text-sm uppercase tracking-[0.3em] text-snow/80">{{ $d['meaning'] }}</p>@endif
                    @if (! empty($d['text']))<p class="font-display text-2xl italic leading-relaxed md:text-3xl">{{ $d['text'] }}</p>@endif
                </div>
            </section>
            @break

        @case('pillars')
            <section class="container-site py-16 md:py-24">
                @if (! empty($d['heading']))
                    <x-site.section-head :eyebrow="$d['eyebrow'] ?? null" :title="$d['heading']" class="mb-12" />
                @endif
                <ol class="grid border-t border-mist sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($d['items'] ?? [] as $i => $item)
                        <li class="flex flex-col gap-3 border-mist py-8 sm:pr-8 lg:border-l lg:px-8 lg:first:border-l-0 lg:first:pl-0">
                            <span class="font-display text-4xl text-ember">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <h3 class="text-lg uppercase tracking-[0.06em]">{{ $item['title'] ?? '' }}</h3>
                            @if (! empty($item['text']))<p class="leading-relaxed text-slate">{{ $item['text'] }}</p>@endif
                        </li>
                    @endforeach
                </ol>
            </section>
            @break

        @case('stats')
            <section class="container-site py-12">
                <dl class="grid grid-cols-2 gap-8 border-y border-mist py-10 md:grid-cols-4">
                    @foreach ($d['items'] ?? [] as $item)
                        <div class="flex flex-col-reverse gap-1">
                            <dt class="text-xs uppercase tracking-[0.12em] text-stone">{{ $item['label'] ?? '' }}</dt>
                            <dd class="font-display text-5xl text-forest">{{ $item['value'] ?? '' }}</dd>
                        </div>
                    @endforeach
                </dl>
            </section>
            @break

        @case('gallery')
            <section class="container-site grid grid-cols-2 gap-3 py-12 md:grid-cols-3 md:gap-4">
                @foreach ($d['images'] ?? [] as $img)
                    <div class="aspect-[4/3] overflow-hidden rounded-2xl"><x-site.media :src="$img" alt="" /></div>
                @endforeach
            </section>
            @break

        @case('cta')
            @php($href = ($d['button_url'] ?? '') === 'booking' ? route('booking') : ($d['button_url'] ?? null))
            <section class="bg-sand">
                <div class="container-site flex flex-col items-center gap-5 py-20 text-center">
                    <h2 class="h-section">{{ $d['heading'] ?? '' }}</h2>
                    @if (! empty($d['text']))<p class="lead max-w-xl">{{ $d['text'] }}</p>@endif
                    @if ($href && ! empty($d['button_label']))
                        <a href="{{ $href }}" class="btn-primary mt-2">{{ $d['button_label'] }}</a>
                    @endif
                </div>
            </section>
            @break
    @endswitch
@endforeach

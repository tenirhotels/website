@props(['href', 'image' => null, 'title', 'meta' => null, 'tags' => [], 'text' => null, 'ratio' => 'aspect-[4/5]', 'cta' => null, 'ctaHref' => null, 'onPhoto' => null])
@php $onPhoto = $onPhoto ?? (bool) $tags; @endphp
{{-- Карточка: впечатление, предложение, история, номер. Название и теги на фото. Карточка с тегами (впечатления) — вся на фото, описание тоже поверх; остальные — белая карточка с текстом под фото. --}}
<article class="group flex h-full flex-col overflow-hidden rounded-2xl border border-mist bg-white">
    <a href="{{ $href }}" class="relative block shrink-0 overflow-hidden {{ $ratio }}">
        <x-site.media :src="$image" :alt="''" class="transition-transform duration-700 group-hover:scale-[1.03]" />
        <span class="pointer-events-none absolute inset-0 bg-[linear-gradient(to_top,rgb(22_33_27/.92)_0%,rgb(22_33_27/.6)_34%,rgb(22_33_27/.0)_68%)]" aria-hidden="true"></span>
        @if ($tags)
            <ul class="absolute left-4 right-4 top-4 flex flex-wrap gap-1.5">
                @foreach ($tags as $tag)
                    <li class="rounded-full bg-white/90 px-3 py-1.5 text-[12px] leading-none text-ink backdrop-blur-sm">{{ $tag }}</li>
                @endforeach
            </ul>
        @endif
        <div class="absolute inset-x-5 bottom-5 text-white">
            <h3 class="flex min-h-[3.1rem] items-end justify-between gap-3 font-display text-[24px] leading-[1.15]">
                <span class="line-clamp-2">{{ $title }}</span>
                <svg class="mb-1 size-5 shrink-0 transition-transform group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg>
            </h3>
            @if ($onPhoto && $text)
                <p class="mt-2 line-clamp-2 min-h-[2.6rem] text-[14px] leading-snug text-white/85">{{ $text }}</p>
            @endif
        </div>
    </a>
    @if ($meta || ($text && ! $onPhoto) || $cta)
        <div class="flex flex-1 flex-col gap-3 p-5">
            @if ($meta)<p class="text-xs uppercase tracking-[0.2em] text-stone">{{ $meta }}</p>@endif
            @if ($text && ! $onPhoto)<p class="line-clamp-4 text-[15px] leading-relaxed text-slate">{{ $text }}</p>@endif
            @if ($cta)
                <a href="{{ $ctaHref ?? $href }}" class="mt-auto inline-block self-start border-b border-current pb-1.5 text-[13px] uppercase tracking-[0.2em] text-ember hover:text-forest">{{ $cta }} →</a>
            @endif
        </div>
    @endif
</article>

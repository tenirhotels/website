@props(['href', 'image' => null, 'title', 'meta' => null, 'tags' => [], 'text' => null, 'ratio' => 'aspect-[4/5]', 'cta' => null, 'ctaHref' => null])
{{-- Карточка: впечатление, предложение, история, номер. Одна белая карточка: название и теги на фото, текст — в той же карточке под фото. --}}
<article class="group flex h-full flex-col overflow-hidden rounded-2xl border border-mist bg-white">
    <a href="{{ $href }}" class="relative block shrink-0 overflow-hidden {{ $ratio }}">
        <x-site.media :src="$image" :alt="''" class="transition-transform duration-700 group-hover:scale-[1.03]" />
        <span class="pointer-events-none absolute inset-0 bg-gradient-to-t from-forest-deep/80 via-forest-deep/20 to-transparent" aria-hidden="true"></span>
        @if ($tags)
            <ul class="absolute left-4 right-4 top-4 flex flex-wrap gap-1.5">
                @foreach ($tags as $tag)
                    <li class="rounded-full bg-white/90 px-3 py-1.5 text-[12px] leading-none text-ink backdrop-blur-sm">{{ $tag }}</li>
                @endforeach
            </ul>
        @endif
        <h3 class="absolute inset-x-5 bottom-4 flex min-h-[3.1rem] items-start justify-between gap-3 font-display text-[24px] leading-[1.15] text-white">
            <span class="line-clamp-2">{{ $title }}</span>
            <svg class="mt-1 size-5 shrink-0 transition-transform group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg>
        </h3>
    </a>
    @if ($meta || $text || $cta)
        <div class="flex flex-1 flex-col gap-3 p-5">
            @if ($meta)<p class="text-xs uppercase tracking-[0.2em] text-stone">{{ $meta }}</p>@endif
            @if ($text)<p class="line-clamp-4 text-[15px] leading-relaxed text-slate">{{ $text }}</p>@endif
            @if ($cta)
                <a href="{{ $ctaHref ?? $href }}" class="mt-auto inline-block self-start border-b border-current pb-1.5 text-[13px] uppercase tracking-[0.2em] text-ember hover:text-forest">{{ $cta }} →</a>
            @endif
        </div>
    @endif
</article>

@props(['href', 'image' => null, 'title', 'meta' => null, 'tags' => [], 'text' => null, 'ratio' => 'aspect-[4/5]', 'cta' => null, 'ctaHref' => null])
{{-- Карточка: впечатление, предложение, история, номер. Название — на фото поверх градиента, теги и текст — под фото. --}}
<article class="group flex flex-col gap-3.5">
    <a href="{{ $href }}" class="relative block overflow-hidden rounded-2xl {{ $ratio }}">
        <x-site.media :src="$image" :alt="''" class="transition-transform duration-700 group-hover:scale-[1.03]" />
        <span class="pointer-events-none absolute inset-0 bg-gradient-to-t from-forest-deep/80 via-forest-deep/20 to-transparent" aria-hidden="true"></span>
        <h3 class="absolute inset-x-5 bottom-4 flex items-end justify-between gap-3 font-display text-[24px] leading-tight text-white">
            <span>{{ $title }}</span>
            <svg class="mb-1 size-5 shrink-0 transition-transform group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg>
        </h3>
    </a>
    @if ($tags)
        <ul class="flex flex-wrap gap-2">
            @foreach ($tags as $tag)
                <li class="rounded-full border border-mist bg-white px-3.5 py-1.5 text-[13px] leading-none text-slate">{{ $tag }}</li>
            @endforeach
        </ul>
    @endif
    @if ($meta)<p class="text-xs uppercase tracking-[0.2em] text-stone">{{ $meta }}</p>@endif
    @if ($text)<p class="text-base leading-relaxed text-slate">{{ $text }}</p>@endif
    @if ($cta)
        <a href="{{ $ctaHref ?? $href }}" class="mt-1 text-[13px] uppercase tracking-[0.2em] text-ember hover:text-forest inline-block border-b border-current pb-1.5">{{ $cta }} →</a>
    @endif
</article>

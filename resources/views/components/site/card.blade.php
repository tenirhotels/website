@props(['href', 'image' => null, 'title', 'meta' => null, 'text' => null, 'ratio' => 'aspect-[4/5]', 'cta' => null, 'ctaHref' => null])
{{-- Карточка: впечатление, предложение, история, номер. --}}
<article class="group flex flex-col gap-4">
    <a href="{{ $href }}" class="block overflow-hidden rounded-2xl {{ $ratio }}" tabindex="-1" aria-hidden="true">
        <x-site.media :src="$image" :alt="$title" class="transition-transform duration-700 group-hover:scale-[1.03]" />
    </a>
    @if ($meta)<p class="text-xs uppercase tracking-[0.2em] text-stone">{{ $meta }}</p>@endif
    <h3 class="font-display text-[24px] leading-tight"><a href="{{ $href }}" class="hover:text-ember">{{ $title }}</a></h3>
    @if ($text)<p class="text-base leading-relaxed text-slate">{{ $text }}</p>@endif
    @if ($cta)
        <a href="{{ $ctaHref ?? $href }}" class="mt-1 text-[13px] uppercase tracking-[0.2em] text-ember hover:text-forest">{{ $cta }} →</a>
    @endif
</article>

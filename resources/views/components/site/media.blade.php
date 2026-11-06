@props(['src' => null, 'alt' => '', 'eager' => false, 'label' => null, 'round' => 'rounded-2xl'])
{{-- Фото или нейтральная заглушка, если фото ещё не загружено в админке. --}}
@if ($url = media($src))
    <img src="{{ $url }}" alt="{{ $alt }}" @if (! $eager) loading="lazy" @else fetchpriority="high" @endif decoding="async"
         {{ $attributes->merge(['class' => 'h-full w-full bg-sand object-cover '.$round]) }}>
@else
    <div {{ $attributes->merge(['class' => 'flex h-full w-full items-end bg-placeholder p-4 '.$round]) }} role="img" aria-label="{{ $alt }}">
        <span class="text-[11px] uppercase tracking-[0.16em] text-slate/80">[{{ $label ?? __('site.common.photo') }}]</span>
    </div>
@endif

@props(['eyebrow' => null, 'title', 'link' => null, 'linkLabel' => null, 'level' => 'h2'])
<div {{ $attributes->merge(['class' => 'flex flex-wrap items-end justify-between gap-6']) }}>
    <div class="flex max-w-3xl flex-col gap-4">
        @if ($eyebrow)<p class="eyebrow">{{ $eyebrow }}</p>@endif
        <{{ $level }} class="h-section">{{ $title }}</{{ $level }}>
        {{ $slot }}
    </div>
    @if ($link)
        <a href="{{ $link }}" class="link-line">{{ $linkLabel ?? __('site.common.view_all') }}</a>
    @endif
</div>

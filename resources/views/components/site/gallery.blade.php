@props(['images' => [], 'alt' => '', 'layout' => 'mosaic'])
{{-- Галерея: мозаика / слайдер / сетка / колонки. Клик открывает просмотр с листанием и увеличением (resources/js/gallery.js). --}}
@php
    $imgs = collect($images)->filter(fn ($i) => $i && media($i))->values();
    $n = $imgs->count();
    $layout = in_array($layout, ['mosaic', 'slider', 'grid', 'masonry'], true) ? $layout : 'mosaic';
    // Мозаика нужна от пяти фото; меньше — аккуратная сетка по числу фото.
    if ($layout === 'mosaic' && $n < 5) { $layout = 'grid'; }
    $thumbSizes = '(min-width: 1024px) 33vw, 50vw';
@endphp
@if ($n)
    <div data-gallery class="relative" role="group" aria-label="{{ $alt }}"
         data-l-close="{{ __('site.common.gallery_close') }}" data-l-prev="{{ __('site.common.gallery_prev') }}" data-l-next="{{ __('site.common.gallery_next') }}" data-l-zoom="{{ __('site.common.gallery_zoom') }}">
        @php
            $item = fn ($img, $cls = '', $extra = '') => 'type="button" data-gallery-item data-src="'.e(media($img)).'" data-alt="'.e($alt).'" class="gallery-item '.$cls.'" '.$extra;
        @endphp

        @if ($layout === 'slider')
            <div data-gallery-track class="gallery-track">
                @foreach ($imgs as $img)
                    <button {!! $item($img) !!} aria-label="{{ $alt }} {{ $loop->iteration }} / {{ $n }}"><x-site.media :src="$img" :alt="$alt" round="" :sizes="'(min-width: 1024px) 40vw, 86vw'" /></button>
                @endforeach
            </div>
            @if ($n > 1)
                <button type="button" class="gallery-arrow is-prev" data-gallery-scroll="-1" aria-label="{{ __('site.common.gallery_prev') }}">&#8249;</button>
                <button type="button" class="gallery-arrow is-next" data-gallery-scroll="1" aria-label="{{ __('site.common.gallery_next') }}">&#8250;</button>
            @endif
        @elseif ($layout === 'masonry')
            <div class="gallery-masonry">
                @foreach ($imgs as $img)
                    <button {!! $item($img) !!} aria-label="{{ $alt }} {{ $loop->iteration }} / {{ $n }}"><x-site.media :src="$img" :alt="$alt" round="" class="h-auto w-full" :sizes="$thumbSizes" /></button>
                @endforeach
            </div>
        @elseif ($layout === 'grid')
            <div @class(['grid gap-2 md:gap-3', 'grid-cols-1' => $n === 1, 'grid-cols-2' => $n !== 1, 'md:grid-cols-3' => $n === 3 || $n > 4])>
                @foreach ($imgs as $img)
                    <button {!! $item($img, $n === 1 ? 'aspect-[4/3] md:aspect-[21/9]' : 'aspect-[4/3]') !!} aria-label="{{ $alt }} {{ $loop->iteration }} / {{ $n }}"><x-site.media :src="$img" :alt="$alt" round="" :sizes="$thumbSizes" /></button>
                @endforeach
            </div>
        @else
            {{-- мозаика: первое фото крупно, до пяти на экране, остальные — в просмотре, на пятом «+N» --}}
            <div class="grid grid-cols-2 gap-2 md:grid-cols-4 md:grid-rows-2 md:gap-3">
                @foreach ($imgs as $img)
                    @php $i = $loop->index; @endphp
                    <button {!! $item($img, ($i === 0 ? 'col-span-2 aspect-[4/3] md:row-span-2 md:aspect-auto' : 'aspect-[4/3]').($i >= 5 ? ' hidden' : '')) !!} aria-label="{{ $alt }} {{ $loop->iteration }} / {{ $n }}">
                        <x-site.media :src="$img" :alt="$alt" round="" :sizes="$i === 0 ? '(min-width: 1024px) 50vw, 100vw' : '(min-width: 1024px) 25vw, 50vw'" />
                        @if ($i === 4 && $n > 5)<span class="gallery-more" aria-hidden="true">+{{ $n - 5 }}</span>@endif
                    </button>
                @endforeach
            </div>
        @endif
    </div>
@endif

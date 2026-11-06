@props(['image' => null, 'eyebrow' => null, 'title', 'text' => null, 'tall' => false])
<section @class(['relative overflow-hidden bg-[#24332B] text-snow', 'h-[78vh] min-h-[560px] max-h-[820px]' => $tall, 'h-[52vh] min-h-[380px] max-h-[560px]' => ! $tall])>
    @if ($image)
        <x-site.media :src="$image" :alt="$title" eager round="" class="absolute inset-0" />
        <div class="absolute inset-0 bg-forest-deep/45" aria-hidden="true"></div>
    @else
        <svg aria-hidden="true" viewBox="0 0 1440 760" preserveAspectRatio="none" class="absolute inset-0 h-full w-full">
            <polygon points="0,560 140,430 230,480 380,300 470,360 610,210 700,270 820,150 930,250 1040,190 1150,300 1260,240 1440,380 1440,760 0,760" fill="#2E4237"/>
            <polygon points="0,640 200,540 330,590 520,470 690,560 880,450 1060,540 1240,470 1440,560 1440,760 0,760" fill="#1F3A2E"/>
        </svg>
    @endif
    <div class="container-site relative flex h-full flex-col justify-end gap-5 pb-20 lg:pb-28">
        @if ($eyebrow)<p class="eyebrow-light">{{ $eyebrow }}</p>@endif
        <h1 @class(['h-display max-w-4xl', 'text-6xl md:text-8xl' => $tall, 'text-5xl md:text-7xl' => ! $tall])>{{ $title }}</h1>
        @if ($text)<p class="max-w-xl text-lg leading-relaxed text-snow/90 md:text-xl">{{ $text }}</p>@endif
        {{ $slot }}
    </div>
</section>

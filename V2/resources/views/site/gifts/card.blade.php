<article class="group flex flex-col gap-4">
    <a href="{{ $g['url'] }}" class="block aspect-[4/3] overflow-hidden rounded-2xl bg-sand" tabindex="-1" aria-hidden="true">
        @if ($g['image'])
            <img src="{{ $g['image'] }}" alt="" loading="lazy" decoding="async" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-[1.03]">
        @else
            <div class="flex h-full w-full items-center justify-center bg-forest text-ember-light"><span class="font-display text-5xl">T</span></div>
        @endif
    </a>
    <p class="text-xs uppercase tracking-[0.2em] text-stone">{{ __('gifts.validity') }}: {{ $g['validity'] }}</p>
    <h3 class="font-display text-[28px] leading-tight"><a href="{{ $g['url'] }}" class="hover:text-ember">{{ $g['name'] }}</a></h3>
    @if ($g['description'])<p class="line-clamp-3 text-base leading-relaxed text-slate">{{ $g['description'] }}</p>@endif
    <div class="mt-1 flex items-center justify-between gap-4">
        <span class="font-display text-3xl text-forest">{{ number_format($g['price'], 0, '.', ' ') }} ₸</span>
        <a href="{{ $g['url'] }}" class="text-[13px] uppercase tracking-[0.2em] text-ember hover:text-forest">{{ __('gifts.give') }} →</a>
    </div>
</article>

<article class="group flex h-full flex-col gap-4">
    <a href="{{ $g['url'] }}" class="block aspect-[4/3] overflow-hidden rounded-2xl bg-sand" tabindex="-1" aria-hidden="true">
        @if ($g['image'])
            <img src="{{ $g['image'] }}" alt="" loading="lazy" decoding="async" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-[1.03]">
        @else
            <div class="flex h-full w-full items-center justify-center bg-forest text-ember-light"><span class="font-display text-5xl">T</span></div>
        @endif
    </a>
    @php
        $name = $g['name'] === mb_strtoupper($g['name']) ? mb_convert_case(mb_strtolower($g['name']), MB_CASE_TITLE) : $g['name'];
    @endphp
    @if ($g['validity'])
        <p class="inline-flex w-fit max-w-full items-center gap-2 rounded-full bg-sand px-3.5 py-1.5 text-[13px] leading-snug text-slate">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 shrink-0 text-ember" aria-hidden="true"><rect x="3.5" y="5" width="17" height="15" rx="2"/><path d="M3.5 10h17M8 3v4M16 3v4"/></svg>
            <span>{{ $g['validity'] }}</span>
        </p>
    @endif
    <h3 class="font-display text-[24px] leading-tight"><a href="{{ $g['url'] }}" class="hover:text-ember">{{ $name }}</a></h3>
    @if ($g['description'])<p class="line-clamp-3 text-base leading-relaxed text-slate">{{ $g['description'] }}</p>@endif
    <div class="mt-auto flex flex-nowrap items-center justify-between gap-3 pt-3">
        <span class="whitespace-nowrap font-display text-[26px] leading-none text-forest sm:text-3xl">{{ number_format($g['price'], 0, '.', ' ') }}<span class="ml-1 font-sans text-[0.6em] text-stone">₸</span></span>
        <a href="{{ $g['url'] }}" class="btn-primary shrink-0 whitespace-nowrap !px-5 !tracking-[0.12em]">{{ __('gifts.give') }}</a>
    </div>
</article>

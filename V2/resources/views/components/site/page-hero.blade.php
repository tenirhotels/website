@props(['image' => null, 'video' => null, 'eyebrow' => null, 'title', 'text' => null, 'tall' => false])
@php
    // Один отель: название отеля над заголовком — лишнее повторение.
    if ($eyebrow && \App\Support\Property::isSingle() && ($single = \App\Support\Property::primary()?->t('name'))) {
        $eyebrow = collect(explode(' · ', $eyebrow))->reject(fn ($part) => trim($part) === $single)->implode(' · ') ?: null;
    }
@endphp
<section @class(['relative overflow-hidden bg-[#24332B] text-snow', 'h-[78vh] min-h-[560px] max-h-[820px]' => $tall, 'h-[52vh] min-h-[380px] max-h-[560px]' => ! $tall])>
    @if ($image)
        <x-site.media :src="$image" :alt="$title" eager round="" class="absolute inset-0" />
        @if ($video)
            {{-- Видео — только на компьютере (от 1024 px), без «экономии трафика» и без «уменьшения анимации».
                 Пока не загрузилось, видно фото; на телефоне видео не грузится вообще. --}}
            <video id="hero-video" data-src="{{ $video }}" muted loop playsinline preload="none" aria-hidden="true" tabindex="-1"
                   class="pointer-events-none absolute inset-0 hidden h-full w-full object-cover opacity-0 transition-opacity duration-700 lg:block"></video>
            <button type="button" id="hero-sound" hidden aria-pressed="false" aria-label="{{ __('site.hero.sound_on') }}" title="{{ __('site.hero.sound_on') }}"
                    class="absolute right-6 top-6 z-10 hidden size-12 items-center justify-center rounded-full border border-white/50 bg-forest-deep/40 text-snow backdrop-blur-sm transition-colors hover:bg-forest-deep/70 lg:right-10 lg:top-8">
                <svg data-off viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 9.5v5h3.5L12 18.5v-13L7.5 9.5z"/><path d="M16 9.5l5 5M21 9.5l-5 5"/></svg>
                <svg data-on viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="hidden"><path d="M4 9.5v5h3.5L12 18.5v-13L7.5 9.5z"/><path d="M15.5 9a4.2 4.2 0 0 1 0 6M18 6.5a7.6 7.6 0 0 1 0 11"/></svg>
            </button>
            <script>
                (function (w) {
                    var v = document.getElementById('hero-video');
                    if (!v || !w.matchMedia('(min-width: 1024px)').matches || w.matchMedia('(prefers-reduced-motion: reduce)').matches || (w.navigator.connection && w.navigator.connection.saveData)) return;
                    // Кнопка звука — только если в файле есть звуковая дорожка.
                    var btn = document.getElementById('hero-sound'), labels = [@json(__('site.hero.sound_on')), @json(__('site.hero.sound_off'))];
                    function hasAudio() {
                        try {
                            if (v.audioTracks && v.audioTracks.length) return true;
                            if (v.mozHasAudio) return true;
                            if (v.webkitAudioDecodedByteCount > 0) return true;
                            var st = v.captureStream ? v.captureStream() : null;
                            if (st && st.getAudioTracks().length) return true;
                        } catch (e) {}
                        return false;
                    }
                    function sound() {
                        if (!btn || !hasAudio()) return;
                        btn.hidden = false; btn.classList.remove('hidden'); btn.classList.add('lg:flex');
                        btn.addEventListener('click', function () {
                            v.muted = !v.muted;
                            if (!v.muted) { var p = v.play(); if (p && p.catch) p.catch(function () {}); }
                            var on = !v.muted;
                            btn.setAttribute('aria-pressed', on ? 'true' : 'false');
                            btn.setAttribute('aria-label', labels[on ? 1 : 0]); btn.title = labels[on ? 1 : 0];
                            btn.querySelector('[data-off]').classList.toggle('hidden', on);
                            btn.querySelector('[data-on]').classList.toggle('hidden', !on);
                        });
                    }
                    function go() {
                        v.src = v.getAttribute('data-src');
                        v.muted = true;
                        v.addEventListener('playing', function () { v.classList.remove('opacity-0'); setTimeout(sound, 600); }, { once: true });
                        var p = v.play(); if (p && p.catch) p.catch(function () {});
                    }
                    function later() { ('requestIdleCallback' in w) ? w.requestIdleCallback(go, { timeout: 3000 }) : w.setTimeout(go, 1000); }
                    if (document.readyState === 'complete') later(); else w.addEventListener('load', later);
                })(window);
            </script>
        @endif
        <div @class(['absolute inset-0', 'bg-forest-deep/45' => $tall, 'bg-[linear-gradient(to_top,rgba(22,33,27,.86)_0%,rgba(22,33,27,.58)_42%,rgba(22,33,27,0)_82%)]' => ! $tall]) aria-hidden="true"></div>
    @else
        <svg aria-hidden="true" viewBox="0 0 1440 760" preserveAspectRatio="none" class="absolute inset-0 h-full w-full">
            <polygon points="0,560 140,430 230,480 380,300 470,360 610,210 700,270 820,150 930,250 1040,190 1150,300 1260,240 1440,380 1440,760 0,760" fill="#2E4237"/>
            <polygon points="0,640 200,540 330,590 520,470 690,560 880,450 1060,540 1240,470 1440,560 1440,760 0,760" fill="#1F3A2E"/>
        </svg>
    @endif
    <div class="container-site relative flex h-full flex-col justify-end gap-5 pb-20 lg:pb-28">
        @if ($eyebrow)<p class="eyebrow-light">{{ $eyebrow }}</p>@endif
        <h1 @class(['h-display max-w-4xl', 'text-5xl md:text-7xl' => $tall, 'text-4xl md:text-6xl' => ! $tall])>{{ $title }}</h1>
        @if ($text)<p class="max-w-xl text-lg leading-relaxed text-snow/90 md:text-xl">{{ $text }}</p>@endif
        {{ $slot }}
    </div>
</section>

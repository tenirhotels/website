@php
    /*
     * Страница ошибки без обращений к базе: должна открываться, даже когда база недоступна.
     * Язык: первый сегмент адреса → cookie выбора языка → язык браузера → казахский (язык по умолчанию).
     */
    $all = ['kk', 'ru', 'en', 'zh'];
    $seg = request()->segment(1);
    $cookie = request()->cookies->get('tenir_lang');
    $loc = in_array($seg, $all, true) ? $seg : (in_array($cookie, $all, true) ? $cookie : (request()->getPreferredLanguage($all) ?: 'kk'));
    $t = fn (string $key) => trans("errors.$key", [], $loc);
    $home = url('/'.$loc);
    $hasBuild = file_exists(public_path('build/manifest.json'));
@endphp
<!DOCTYPE html>
<html lang="{{ $loc }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ $code }} · {{ $t("$code.title") }} — Tenir Hotels</title>
    @if ($hasBuild) @vite(['resources/css/app.css']) @endif
</head>
<body class="relative min-h-screen overflow-hidden bg-forest-deep text-snow">
    <div class="pattern-tile bg-white/[0.05]" aria-hidden="true"></div>
    <div class="absolute inset-x-0 bottom-0 h-1/2 bg-[linear-gradient(to_top,rgb(22_33_27)_10%,rgb(22_33_27/0)_100%)]" aria-hidden="true"></div>

    <div class="relative mx-auto flex min-h-screen max-w-3xl flex-col px-6 py-8">
        <a href="{{ $home }}" class="inline-block self-start" aria-label="Tenir Hotels">
            <img src="/images/logo-light.svg" alt="Tenir Eco Hotels" class="h-12 w-auto">
        </a>

        <main class="flex flex-1 flex-col justify-center py-12">
            <p class="text-[13px] uppercase tracking-[0.3em] text-ember-light">{{ $t('altitude') }}</p>

            <p class="mt-3 font-display text-[clamp(96px,26vw,200px)] leading-[0.9] tracking-tight text-white" aria-hidden="true">{{ $code }}</p>

            {{-- Горный гребень с высотной отметкой --}}
            <svg viewBox="0 0 600 70" preserveAspectRatio="xMinYMid meet" class="mt-1 h-14 w-full max-w-xl text-ember-light" fill="none" aria-hidden="true">
                <path d="M0 62 L70 40 L120 52 L210 14 L270 46 L330 28 L400 58 L470 22 L540 50 L600 36" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                <circle cx="210" cy="14" r="3.5" fill="currentColor"/>
                <circle cx="470" cy="22" r="3.5" fill="currentColor" opacity=".5"/>
            </svg>

            <h1 class="mt-8 font-display text-3xl leading-tight text-white md:text-5xl">{{ $t("$code.title") }}</h1>
            <p class="mt-4 max-w-xl text-[18px] leading-relaxed text-snow/80">{{ $t("$code.text") }}</p>

            <div class="mt-9 flex flex-wrap gap-3">
                <a href="{{ $home }}" class="btn-primary">{{ $t('home') }}</a>
                @if ($code === 404 || $code === 403)
                    <a href="{{ $home }}/rooms" class="btn-ghost">{{ $t('rooms') }}</a>
                @else
                    <a href="javascript:location.reload()" class="btn-ghost">{{ $t('retry') }}</a>
                @endif
            </div>
        </main>

        <footer class="border-t border-white/15 pt-5 text-[15px] text-snow/70">
            {{ $t('help') }}
            <a href="tel:+77273131415" class="ml-2 whitespace-nowrap text-white hover:text-ember-light">+7 727 313 14 15</a>
            <a href="mailto:reservations@tenirhotels.com" class="ml-3 whitespace-nowrap text-white hover:text-ember-light">reservations@tenirhotels.com</a>
        </footer>
    </div>
</body>
</html>

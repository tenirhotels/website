@php
    use App\Models\Setting;
    use App\Support\Gifts;

    $phone = Setting::get('contacts.phone');
    $email = Setting::get('contacts.email');
    $whatsapp = Setting::get('contacts.whatsapp');

    // Соцсети — отдельной полосой: порядок и значки; неизвестная сеть выводится текстом.
    $networks = [
        'instagram' => ['Instagram', '<rect x="3.5" y="3.5" width="17" height="17" rx="5" /><circle cx="12" cy="12" r="4" /><circle cx="17.2" cy="6.8" r="0.6" fill="currentColor" />'],
        'youtube' => ['YouTube', '<rect x="2.5" y="5.5" width="19" height="13" rx="4" /><path d="M10 9.2v5.6l4.8-2.8z" fill="currentColor" />'],
        'tiktok' => ['TikTok', '<path d="M14.5 3.5c.3 2.3 1.7 3.8 4 4v2.9a7 7 0 0 1-3.9-1.3v6a5.2 5.2 0 1 1-5.2-5.2c.3 0 .6 0 .9.1v3a2.3 2.3 0 1 0 1.5 2.1V3.5z" />'],
        'soundcloud' => ['SoundCloud', '<path d="M3 14.5v2M6 12.5v4M9 10.5v6M12 9v7.5h7.2a3.3 3.3 0 0 0 .4-6.6A5.2 5.2 0 0 0 12 9z" />'],
        'facebook' => ['Facebook', '<path d="M14 21v-8h2.7l.4-3.2H14V7.9c0-.9.3-1.5 1.6-1.5h1.7V3.5c-.3 0-1.3-.1-2.4-.1-2.4 0-4 1.5-4 4.1v2.3H8v3.2h2.9v8z" />'],
    ];
    $social = collect(Setting::get('social', []) ?: [])->filter();
    $social = $social->sortBy(fn ($url, $key) => array_search($key, array_keys($networks), true) === false ? 99 : array_search($key, array_keys($networks), true));
    $brandPages = $navPages->whereIn('placement', ['main', 'brand'])->sortBy('sort');
    $legalPages = $navPages->where('placement', 'footer');
@endphp
<footer class="relative isolate overflow-hidden bg-forest-deep text-[#D8DCD8]">
    <div class="pattern-tile -z-10 bg-white opacity-[0.035]" aria-hidden="true"></div>
    <div class="container-site flex flex-col gap-10 pb-8 pt-16 md:gap-14 md:pt-20">
        <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-[1.4fr_1fr_1fr_1fr]">
            <div class="flex flex-col gap-5">
                <img src="/images/logo-light.svg" alt="Tenir" width="150" height="36" class="h-9 w-auto self-start">
                @if ($text = Setting::text('brand.footer_text'))
                    <p class="max-w-sm text-base leading-relaxed text-[#B4BCB7]">{{ $text }}</p>
                @endif
            </div>

            <div class="flex flex-col gap-3 text-base">
                <p class="text-xs uppercase tracking-[0.2em] text-[#9AA59F]">{{ __('site.footer.reservations') }}</p>
                @if ($phone)<a href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}" class="hover:text-white">{{ $phone }}</a>@endif
                @if ($whatsapp)<a href="https://wa.me/{{ preg_replace('/\D/', '', $whatsapp) }}" class="hover:text-white">WhatsApp</a>@endif
                @if ($email)<a href="mailto:{{ $email }}" class="hover:text-white">{{ $email }}</a>@endif
                @if ($hours = Setting::get('contacts.hours'))<span class="text-[#9AA59F]">{{ $hours }}</span>@endif
            </div>

            <nav aria-label="{{ __('site.footer.guests') }}" class="flex flex-col gap-3 text-base">
                <p class="text-xs uppercase tracking-[0.2em] text-[#9AA59F]">{{ __('site.footer.guests') }}</p>
                @if (\App\Support\Property::isSingle() && ($single = \App\Support\Property::primary()))
                    {{-- Один отель: ссылка на главную бессмысленна — ведём в разделы отеля. --}}
                    <a href="{{ $single->sectionUrl('rooms') }}" class="hover:text-white">{{ __('site.hotel.rooms') }}</a>
                    <a href="{{ $single->sectionUrl('dining') }}" class="hover:text-white">{{ __('site.hotel.dining') }}</a>
                    <a href="{{ route('experiences.index') }}" class="hover:text-white">{{ __('site.nav.experiences') }}</a>
                @else
                    @foreach ($navHotels as $h)
                        <a href="{{ $h->url() }}" class="hover:text-white">{{ $h->t('name') }}</a>
                    @endforeach
                @endif
                <a href="{{ route('offers.index') }}" class="hover:text-white">{{ __('site.nav.offers') }}</a>
                <a href="{{ route('events.index') }}" class="hover:text-white">{{ __('site.nav.events') }}</a>
                @if (Gifts::offers())<a href="{{ route('gifts') }}" class="hover:text-white">{{ __('gifts.open') }}</a>@endif
                <a href="{{ route('map') }}" class="hover:text-white">{{ __('map.open_map') }}</a>
                <a href="{{ route('stories.index') }}" class="hover:text-white">{{ __('site.nav.stories') }}</a>
                <a href="{{ route('contact') }}" class="hover:text-white">{{ __('site.nav.contact') }}</a>
            </nav>

            {{-- Бренд: философия и устойчивое развитие (страницы с размещением «основное меню»). --}}
            <nav aria-label="{{ __('site.footer.brand') }}" class="flex flex-col gap-3 text-base">
                <p class="text-xs uppercase tracking-[0.2em] text-[#9AA59F]">{{ __('site.footer.brand') }}</p>
                @foreach ($brandPages as $page)
                    <a href="{{ $page->url() }}" class="hover:text-white">{{ $page->t('title') }}</a>
                @endforeach
            </nav>
        </div>

        {{-- Соцсети отдельной полосой --}}
        @if ($social->isNotEmpty())
            <div class="flex flex-col gap-5 border-t border-[#2E3A33] pt-8 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-xs uppercase tracking-[0.2em] text-[#9AA59F]">{{ __('site.footer.follow') }}</p>
                <ul class="flex flex-wrap items-center gap-3">
                    @foreach ($social as $network => $url)
                        @php([$label, $icon] = $networks[$network] ?? [ucfirst($network), null])
                        <li>
                            <a href="{{ $url }}" target="_blank" rel="noopener" aria-label="{{ $label }}" title="{{ $label }}"
                               class="flex size-12 items-center justify-center rounded-full border border-[#3A4A41] text-[#D8DCD8] transition-colors hover:border-ember-light hover:text-ember-light">
                                @if ($icon)
                                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $icon !!}</svg>
                                @else
                                    <span class="text-xs">{{ mb_substr($label, 0, 2) }}</span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex flex-wrap items-center justify-between gap-x-8 gap-y-3 border-t border-[#2E3A33] pt-6 text-[13px] text-[#9AA59F]">
            <span>© {{ now()->year }} {{ Setting::text('brand.name', 'Tenir Hotels') }}@if ($legal = Setting::get('brand.legal_name')) · {{ $legal }}@endif. {{ __('site.footer.rights') }}</span>
            @if ($legalPages->isNotEmpty())
                <nav class="flex flex-wrap gap-x-6 gap-y-2" aria-label="Legal">
                    @foreach ($legalPages as $page)
                        <a href="{{ $page->url() }}" class="hover:text-white">{{ $page->t('title') }}</a>
                    @endforeach
                </nav>
            @endif
        </div>
    </div>
</footer>

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
    <div class="container-site flex flex-col gap-8 pb-7 pt-12 md:gap-10 md:pt-14">
        <div class="grid grid-cols-2 gap-x-8 gap-y-10 lg:grid-cols-[1.35fr_1fr_1fr_1fr_1fr]">
            {{-- Бренд, подпись и соцсети --}}
            <div class="col-span-2 flex flex-col gap-4 lg:col-span-1">
                <img src="/images/logo-light.svg" alt="Tenir" width="150" height="36" class="h-9 w-auto self-start">
                @if ($text = Setting::text('brand.footer_text'))
                    <p class="max-w-xs text-[15px] leading-relaxed text-[#B4BCB7]">{{ $text }}</p>
                @endif
                @if ($social->isNotEmpty())
                    <ul class="mt-2 flex flex-wrap items-center gap-2.5" aria-label="{{ __('site.footer.follow') }}">
                        @foreach ($social as $network => $url)
                            @php([$label, $icon] = $networks[$network] ?? [ucfirst($network), null])
                            <li>
                                <a href="{{ $url }}" target="_blank" rel="noopener" aria-label="{{ $label }}" title="{{ $label }}"
                                   class="flex size-10 items-center justify-center rounded-full border border-[#3A4A41] text-[#D8DCD8] transition-colors hover:border-ember-light hover:text-ember-light">
                                    @if ($icon)
                                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $icon !!}</svg>
                                    @else
                                        <span class="text-xs">{{ mb_substr($label, 0, 2) }}</span>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- Бронирование и связь --}}
            <div class="col-span-2 flex flex-col gap-2.5 text-[15px] sm:col-span-1">
                <p class="mb-1 text-xs uppercase tracking-[0.2em] text-[#9AA59F]">{{ __('site.footer.reservations') }}</p>
                @if ($phone)<a href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}" class="hover:text-white">{{ $phone }}</a>@endif
                @if ($whatsapp)<a href="{{ wa_url($whatsapp, 'stay') }}" class="hover:text-white">WhatsApp</a>@endif
                @if ($email)<a href="mailto:{{ $email }}" class="hover:text-white">{{ email_wrap($email) }}</a>@endif
                @if ($hours = Setting::get('contacts.hours'))<span class="text-[#9AA59F]">{{ $hours }}</span>@endif
            </div>

            <nav aria-label="{{ __('site.footer.guests') }}" class="flex flex-col gap-2.5 text-[15px]">
                <p class="mb-1 text-xs uppercase tracking-[0.2em] text-[#9AA59F]">{{ __('site.footer.guests') }}</p>
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
            </nav>

            {{-- Планирование поездки: практическая информация. --}}
            <nav aria-label="{{ __('site.footer.plan') }}" class="flex flex-col gap-2.5 text-[15px]">
                <p class="mb-1 text-xs uppercase tracking-[0.2em] text-[#9AA59F]">{{ __('site.footer.plan') }}</p>
                @if ($ph = (\App\Support\Property::isSingle() ? \App\Support\Property::primary() : null))
                    <a href="{{ $ph->sectionUrl('getting-here') }}" class="hover:text-white">{{ __('site.hotel.getting_here') }}</a>
                    <a href="{{ $ph->sectionUrl('before-you-arrive') }}" class="hover:text-white">{{ __('site.hotel.before_arrive') }}</a>
                @endif
                @if (\App\Support\Property::mapHotel())<a href="{{ route('map') }}" class="hover:text-white">{{ __('map.open_map') }}</a>@endif
                @if ($ph)<a href="{{ $ph->sectionUrl('gallery') }}" class="hover:text-white">{{ __('site.hotel.gallery') }}</a>@endif
                <a href="{{ route('contact') }}" class="hover:text-white">{{ __('site.nav.contact') }}</a>
            </nav>

            {{-- О нас: бренд, истории, отзывы. На телефоне — компактной строкой. --}}
            <nav aria-label="{{ __('site.footer.about') }}" class="col-span-2 flex flex-col gap-2.5 text-[15px] lg:col-span-1">
                <p class="mb-1 text-xs uppercase tracking-[0.2em] text-[#9AA59F]">{{ __('site.footer.about') }}</p>
                <div class="flex flex-wrap gap-x-6 gap-y-2.5 lg:flex-col lg:gap-y-2.5">
                    @foreach ($brandPages as $page)
                        <a href="{{ $page->url() }}" class="hover:text-white">{{ $page->t('title') }}</a>
                    @endforeach
                    <a href="{{ route('stories.index') }}" class="hover:text-white">{{ __('site.nav.stories') }}</a>
                    @if (\App\Support\Property::isSingle() && ($rh = \App\Support\Property::primary()))<a href="{{ $rh->sectionUrl('reviews') }}" class="hover:text-white">{{ __('site.hotel.reviews') }}</a>@endif
                </div>
            </nav>
        </div>

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

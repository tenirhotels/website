@php
    use App\Models\Setting;
    $social = array_filter(Setting::get('social', []) ?: []);
    $phone = Setting::get('contacts.phone');
    $email = Setting::get('contacts.email');
    $whatsapp = Setting::get('contacts.whatsapp');
@endphp
<footer class="relative isolate overflow-hidden bg-forest-deep text-[#D8DCD8]">
    <div class="pattern-tile -z-10 bg-white opacity-[0.035]" aria-hidden="true"></div>
    <div class="container-site flex flex-col gap-8 md:gap-14 pb-10 pt-20">
        <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-[1.4fr_1fr_1fr_1fr]">
            <div class="flex flex-col gap-5">
                <img src="/images/logo-light.svg" alt="Tenir" width="150" height="36" class="h-9 w-auto self-start">
                @if ($text = Setting::text('brand.footer_text'))
                    <p class="max-w-sm text-[15px] leading-relaxed text-[#B4BCB7]">{{ $text }}</p>
                @endif
            </div>

            <div class="flex flex-col gap-3 text-[15px]">
                <p class="text-xs uppercase tracking-[0.2em] text-[#9AA59F]">{{ __('site.footer.reservations') }}</p>
                @if ($phone)<a href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}" class="hover:text-white">{{ $phone }}</a>@endif
                @if ($whatsapp)<a href="https://wa.me/{{ preg_replace('/\D/', '', $whatsapp) }}" class="hover:text-white">WhatsApp</a>@endif
                @if ($email)<a href="mailto:{{ $email }}" class="hover:text-white">{{ $email }}</a>@endif
                @if ($hours = Setting::get('contacts.hours'))<span class="text-[#9AA59F]">{{ $hours }}</span>@endif
            </div>

            <nav aria-label="{{ __('site.footer.guests') }}" class="flex flex-col gap-3 text-[15px]">
                <p class="text-xs uppercase tracking-[0.2em] text-[#9AA59F]">{{ __('site.footer.guests') }}</p>
                @foreach ($navHotels as $h)
                    <a href="{{ $h->url() }}" class="hover:text-white">{{ $h->t('name') }}</a>
                @endforeach
                <a href="{{ route('offers.index') }}" class="hover:text-white">{{ __('site.nav.offers') }}</a>
                <a href="{{ route('stories.index') }}" class="hover:text-white">{{ __('site.nav.stories') }}</a>
                <a href="{{ route('contact') }}" class="hover:text-white">{{ __('site.nav.contact') }}</a>
            </nav>

            <nav aria-label="{{ __('site.footer.brand') }}" class="flex flex-col gap-3 text-[15px]">
                <p class="text-xs uppercase tracking-[0.2em] text-[#9AA59F]">{{ __('site.footer.brand') }}</p>
                @foreach ($navPages as $page)
                    <a href="{{ $page->url() }}" class="hover:text-white">{{ $page->t('title') }}</a>
                @endforeach
                @foreach ($social as $network => $url)
                    <a href="{{ $url }}" class="hover:text-white" rel="noopener" target="_blank">{{ ucfirst($network) }}</a>
                @endforeach
            </nav>
        </div>

        <div class="flex flex-wrap justify-between gap-4 border-t border-[#2E3A33] pt-6 text-[13px] text-[#9AA59F]">
            <span>© {{ now()->year }} {{ Setting::text('brand.name', 'Tenir Hotels') }}@if ($legal = Setting::get('brand.legal_name')) · {{ $legal }}@endif. {{ __('site.footer.rights') }}</span>
        </div>
    </div>
</footer>

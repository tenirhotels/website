@php use App\Models\Setting; @endphp
<x-layouts.site :seo="$seo">
    <x-site.page-hero :title="__('site.nav.contact')" :text="__('site.booking.help')" />

    <section class="container-site grid gap-12 py-12 md:py-16 lg:py-20 md:grid-cols-2 lg:grid-cols-3">
        <div class="flex flex-col gap-3 text-[17px]">
            <h2 class="mb-3 font-display text-2xl">{{ __('site.footer.reservations') }}</h2>
            @if ($phone = Setting::get('contacts.phone'))<a href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}" class="hover:text-ember">{{ $phone }}</a>@endif
            @if ($wa = Setting::get('contacts.whatsapp'))<a href="{{ wa_url($wa, 'stay') }}" class="hover:text-ember">WhatsApp</a>@endif
            @if ($email = Setting::get('contacts.email'))<a href="mailto:{{ $email }}" class="hover:text-ember">{{ email_wrap($email) }}</a>@endif
            @if ($hours = Setting::get('contacts.hours'))<p class="text-stone">{{ $hours }}</p>@endif
        </div>
        @if (Setting::get('contacts.sales_phone') || Setting::get('contacts.sales_email'))
            <div class="flex flex-col gap-3 text-[17px]">
                <h2 class="mb-3 font-display text-2xl">{{ __('site.footer.sales') }}</h2>
                @if ($sp = Setting::get('contacts.sales_phone'))<a href="tel:{{ preg_replace('/[^\d+]/', '', $sp) }}" class="hover:text-ember">{{ $sp }}</a>@endif
                @if ($se = Setting::get('contacts.sales_email'))<a href="mailto:{{ $se }}" class="hover:text-ember">{{ $se }}</a>@endif
            </div>
        @endif
        @foreach ($hotels as $h)
            <div class="flex flex-col gap-3 text-[17px]">
                <h2 class="mb-3 font-display text-2xl"><a href="{{ $h->url() }}" class="hover:text-ember">{{ $h->t('name') }}</a></h2>
                @if ($h->t('address'))<p class="text-slate">{{ $h->t('address') }}</p>@endif
                @if ($h->phone)<a href="tel:{{ preg_replace('/[^\d+]/', '', $h->phone) }}" class="hover:text-ember">{{ $h->phone }}</a>@endif
                @if ($h->email)<a href="mailto:{{ $h->email }}" class="hover:text-ember">{{ $h->email }}</a>@endif
                <a href="{{ $h->sectionUrl('getting-here') }}" class="link-line mt-2 self-start">{{ __('site.hotel.getting_here') }}</a>
            </div>
        @endforeach
    </section>
</x-layouts.site>

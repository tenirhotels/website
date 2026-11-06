@props(['cta' => 'booking', 'title' => '', 'hotel' => null])
                @php
                    $digits = fn ($v) => preg_replace('/[^\d+]/', '', (string) $v);
                    $cta = $cta ?: 'booking';
                    $salesPhone = \App\Models\Setting::get('contacts.sales_phone');
                    $salesEmail = \App\Models\Setting::get('contacts.sales_email');
                    $phone = $hotel?->phone ?? \App\Models\Setting::get('contacts.phone');
                    $wa = $hotel?->whatsapp ?? \App\Models\Setting::get('contacts.whatsapp');
                    $email = \App\Models\Setting::get('contacts.email');
                    if ($cta === 'sales' && ! $salesPhone && ! $salesEmail) { $cta = 'contact'; }
                    $subject = rawurlencode($title);
                @endphp
                @if ($cta === 'sales')
                    <div class="mt-2 border-t border-mist pt-5">
                        <dt class="field-label mb-1">{{ __('site.cta.request_label') }}</dt>
                        <dd class="text-stone">{{ __('site.cta.request_text') }}</dd>
                    </div>
                    @if ($salesEmail)<a href="mailto:{{ $salesEmail }}?subject={{ $subject }}" class="btn-primary">{{ __('site.cta.send_request') }}</a>@endif
                    <div class="flex flex-col gap-1">
                        @if ($salesPhone)<a href="tel:{{ $digits($salesPhone) }}" class="hover:text-ember">{{ $salesPhone }}</a>@endif
                        @if ($salesEmail)<a href="mailto:{{ $salesEmail }}?subject={{ $subject }}" class="hover:text-ember">{{ $salesEmail }}</a>@endif
                    </div>
                @elseif ($cta === 'contact')
                    <div class="mt-2 border-t border-mist pt-5">
                        <dt class="field-label mb-1">{{ __('site.cta.ask_label') }}</dt>
                        <dd class="text-stone">{{ __('site.cta.ask_text') }}</dd>
                    </div>
                    @if ($phone)<a href="tel:{{ $digits($phone) }}" class="btn-primary">{{ __('site.cta.call') }} · {{ $phone }}</a>@endif
                    <div class="flex flex-col gap-1">
                        @if ($wa)<a href="https://wa.me/{{ preg_replace('/\D/', '', $wa) }}" class="hover:text-ember">WhatsApp</a>@endif
                        @if ($email)<a href="mailto:{{ $email }}?subject={{ $subject }}" class="hover:text-ember">{{ $email }}</a>@endif
                    </div>
                @elseif ($cta !== 'none')
                    <a href="{{ route('booking', array_filter(['hotel' => $hotel?->slug])) }}" class="btn-primary mt-2">{{ __('site.common.book_now') }}</a>
                @endif

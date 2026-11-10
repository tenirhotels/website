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
                @if ($cta === 'sales' || $cta === 'contact')
                    @php
                        $sales = $cta === 'sales';
                        $rows = $sales
                            ? [
                                $salesEmail ? ['mail', __('site.cta.send_request'), $salesEmail, 'mailto:'.$salesEmail.'?subject='.$subject, false] : null,
                                $salesPhone ? ['phone', __('site.cta.call'), $salesPhone, 'tel:'.$digits($salesPhone), false] : null,
                            ]
                            : [
                                $phone ? ['phone', __('site.cta.call'), $phone, 'tel:'.$digits($phone), false] : null,
                                $wa ? ['chat', 'WhatsApp', $wa, 'https://wa.me/'.preg_replace('/\D/', '', $wa), true] : null,
                                $email ? ['mail', __('site.cta.write_email'), $email, 'mailto:'.$email.'?subject='.$subject, false] : null,
                            ];
                        $hours = \App\Models\Setting::get('contacts.hours');
                    @endphp
                    <div class="mt-2 border-t border-mist pt-5">
                        <dt class="field-label mb-1">{{ $sales ? __('site.cta.request_label') : __('site.cta.ask_label') }}</dt>
                        <dd class="text-stone">{{ $sales ? __('site.cta.request_text') : __('site.cta.ask_text') }}</dd>
                    </div>
                    <ul class="flex flex-col gap-2.5">
                        @foreach (array_filter($rows) as [$icon, $label, $value, $href, $blank])
                            <li>
                                <a href="{{ $href }}" @if ($blank) target="_blank" rel="noopener" @endif
                                   class="group flex items-center gap-4 rounded-2xl border border-mist bg-white px-4 py-3.5 transition hover:border-ember">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-sand text-ember transition group-hover:bg-ember group-hover:text-white">{{ site_icon($icon, 'h-5 w-5') }}</span>
                                    <span class="flex min-w-0 flex-col">
                                        <span class="text-xs uppercase tracking-[0.14em] text-stone">{{ $label }}</span>
                                        <span class="break-words text-lg leading-snug text-ink">{{ $value }}</span>
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    @if ($hours)<p class="text-sm text-stone">{{ $hours }}</p>@endif
                @elseif ($cta !== 'none')
                    <a href="{{ route('booking', array_filter(['hotel' => $hotel?->slug])) }}" class="btn-primary mt-2">{{ __('site.common.book_now') }}</a>
                @endif

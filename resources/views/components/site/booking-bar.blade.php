@props(['hotel' => null, 'id' => 'bb'])
@php
    // Плашка бронирования: отель и промокод. Даты и гостей выбирают в календаре модуля Exely
    // на странице /booking — там сразу видны цены и наличие номеров.
    $hotels = $navHotels->where('status', \App\Models\Hotel::STATUS_OPEN)->values();
    $selected = $hotel?->slug ?? $hotels->first()?->slug;
    $cell = 'flex flex-1 basis-48 flex-col justify-center gap-1.5 border-mist px-6 py-5 lg:border-r';
@endphp
<form action="{{ route('booking') }}" method="get" data-booking-form
      aria-label="{{ __('site.booking.title') }}"
      class="flex flex-wrap items-stretch bg-white shadow-[0_12px_40px_rgba(22,33,27,0.14)]">

    @if ($hotels->count() > 1)
        <div class="{{ $cell }}">
            <label for="{{ $id }}-hotel" class="field-label">{{ __('site.booking.hotel') }}</label>
            <select id="{{ $id }}-hotel" name="hotel" class="field">
                @foreach ($hotels as $h)
                    <option value="{{ $h->slug }}" @selected($h->slug === $selected)>{{ $h->t('name') }}</option>
                @endforeach
            </select>
        </div>
    @elseif ($selected)
        <input type="hidden" name="hotel" value="{{ $selected }}">
        <div class="{{ $cell }}">
            <span class="field-label">{{ __('site.booking.hotel') }}</span>
            <span class="text-lg">{{ $hotels->first()->t('name') }}</span>
        </div>
    @endif

    <div class="{{ $cell }}">
        <button type="button" data-promo-toggle class="self-start text-[13px] text-stone underline underline-offset-4 hover:text-ember">{{ __('site.booking.promo_add') }}</button>
        <div data-promo-field hidden class="flex flex-col gap-1.5">
            <label for="{{ $id }}-promo" class="field-label">{{ __('site.booking.promo') }}</label>
            <input id="{{ $id }}-promo" type="text" name="promo_code" maxlength="64" class="field border-b border-mist text-base" autocomplete="off">
        </div>
    </div>

    <button type="submit" class="btn-primary min-w-56 px-8 py-6 text-sm">{{ __('site.booking.search') }}</button>
</form>

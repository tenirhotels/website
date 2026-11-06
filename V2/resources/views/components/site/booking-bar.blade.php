@props(['hotel' => null, 'variant' => 'inline', 'id' => 'bb'])
@php
    // Плашка бронирования (как у Six Senses / Marriott): отель, даты, гости, промокод.
    // Отправляет понятные параметры на /{locale}/booking — там они переводятся для модуля Exely.
    $hotels = $navHotels->where('status', \App\Models\Hotel::STATUS_OPEN)->values();
    $selected = $hotel?->slug ?? $hotels->first()?->slug;
    $stacked = $variant === 'stacked';
    $cell = $stacked ? 'border-b border-mist py-4' : 'flex-1 basis-40 border-mist px-6 py-5 lg:border-r';
@endphp
<form action="{{ route('booking') }}" method="get" data-booking-form
      aria-label="{{ __('site.booking.title') }}"
      @class(['bg-white', 'flex flex-wrap items-stretch shadow-[0_12px_40px_rgba(22,33,27,0.14)]' => ! $stacked, 'flex flex-col' => $stacked])>

    @if ($hotels->count() > 1)
        <div class="{{ $cell }} flex flex-col gap-1.5">
            <label for="{{ $id }}-hotel" class="field-label">{{ __('site.booking.hotel') }}</label>
            <select id="{{ $id }}-hotel" name="hotel" class="field">
                @foreach ($hotels as $h)
                    <option value="{{ $h->slug }}" @selected($h->slug === $selected)>{{ $h->t('name') }}</option>
                @endforeach
            </select>
        </div>
    @elseif ($selected)
        <input type="hidden" name="hotel" value="{{ $selected }}">
        @unless ($stacked)
            <div class="{{ $cell }} flex flex-col justify-center gap-1.5">
                <span class="field-label">{{ __('site.booking.hotel') }}</span>
                <span class="text-lg">{{ $hotels->first()->t('name') }}</span>
            </div>
        @endunless
    @endif

    <div class="{{ $cell }} flex flex-col gap-1.5">
        <label for="{{ $id }}-in" class="field-label">{{ __('site.booking.check_in') }}</label>
        <input id="{{ $id }}-in" type="date" name="check_in" required class="field">
    </div>
    <div class="{{ $cell }} flex flex-col gap-1.5">
        <label for="{{ $id }}-out" class="field-label">{{ __('site.booking.check_out') }}</label>
        <input id="{{ $id }}-out" type="date" name="check_out" required class="field">
    </div>
    <div class="{{ $cell }} flex gap-6">
        <div class="flex flex-1 flex-col gap-1.5">
            <label for="{{ $id }}-adults" class="field-label">{{ __('site.booking.adults') }}</label>
            <select id="{{ $id }}-adults" name="adults" class="field">
                @foreach (range(1, config('tenir.exely.max_adults')) as $n)
                    <option value="{{ $n }}" @selected($n === 2)>{{ $n }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex flex-1 flex-col gap-1.5">
            <label for="{{ $id }}-children" class="field-label">{{ __('site.booking.children') }}</label>
            <select id="{{ $id }}-children" name="children" class="field">
                @foreach (range(0, config('tenir.exely.max_children')) as $n)
                    <option value="{{ $n }}">{{ $n }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div @class(['flex flex-col justify-center gap-2', 'px-6 py-4' => ! $stacked, 'py-4' => $stacked])>
        <button type="button" data-promo-toggle class="self-start text-[13px] text-stone underline underline-offset-4 hover:text-ember">{{ __('site.booking.promo_add') }}</button>
        <div data-promo-field hidden class="flex flex-col gap-1.5">
            <label for="{{ $id }}-promo" class="field-label">{{ __('site.booking.promo') }}</label>
            <input id="{{ $id }}-promo" type="text" name="promo_code" maxlength="64" class="field border-b border-mist text-base" autocomplete="off">
        </div>
    </div>

    <button type="submit" @class(['btn-primary text-sm', 'min-w-56 px-8 py-6' => ! $stacked, 'mt-2 w-full py-5' => $stacked])>{{ __('site.booking.search') }}</button>
</form>

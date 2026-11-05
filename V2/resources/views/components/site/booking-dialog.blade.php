@props(['hotel' => null])
<dialog id="booking-dialog" aria-labelledby="booking-dialog-title"
        class="m-auto w-full max-w-lg bg-white p-0 backdrop:bg-forest-deep/60 max-sm:mb-0 max-sm:max-w-none">
    <div class="flex flex-col gap-2 p-6 sm:p-8">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="eyebrow">{{ __('site.booking.best_rate') }}</p>
                <h2 id="booking-dialog-title" class="mt-2 font-display text-3xl">{{ __('site.booking.title') }}</h2>
            </div>
            <button type="button" data-close-booking class="-mr-2 flex size-11 items-center justify-center" aria-label="{{ __('site.nav.close') }}">
                <svg aria-hidden="true" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 4l12 12M16 4 4 16"/></svg>
            </button>
        </div>
        <x-site.booking-bar :hotel="$hotel" variant="stacked" id="bd" />
    </div>
</dialog>

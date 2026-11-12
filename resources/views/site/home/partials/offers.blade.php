@if (\App\Models\Setting::get('home.offers', true) !== false && $offers->isNotEmpty())
    <section class="band band-sand bg-sand">
        <div class="container-site flex flex-col gap-8 md:gap-12 py-14 md:py-20 lg:py-24">
            <x-site.section-head :eyebrow="__('site.nav.offers')" :title="__('site.home.offers_title')" :link="\App\Models\Offer::active()->count() > $offers->count() ? route('offers.index') : null" />
            <div class="rail sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($offers as $o)
                    @include('site.offers.partials.card', ['offer' => $o])
                @endforeach
            </div>
        </div>
    </section>
@endif

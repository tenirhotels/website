@if ($offers->isNotEmpty())
    <section class="bg-sand">
        <div class="container-site flex flex-col gap-12 py-24">
            <x-site.section-head :eyebrow="__('site.nav.offers')" :title="__('site.home.offers_title')" :link="route('offers.index')" />
            <div class="grid gap-8 md:grid-cols-3">
                @foreach ($offers as $o)
                    @include('site.offers.partials.card', ['offer' => $o])
                @endforeach
            </div>
        </div>
    </section>
@endif

@if ($events->isNotEmpty())
    <section class="bg-snow">
        <div class="container-site flex flex-col gap-8 py-14 md:gap-12 md:py-20 lg:py-24">
            <x-site.section-head :eyebrow="__('site.nav.events')" :title="__('site.home.events_title')" :link="\App\Models\Event::upcoming()->count() > $events->count() ? route('events.index') : null" :link-label="__('site.common.view_all')" />
            <div class="border-b border-mist">
                @foreach ($events as $e)@include('site.events.partials.row', ['event' => $e])@endforeach
            </div>
        </div>
    </section>
@endif

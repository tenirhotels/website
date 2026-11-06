<x-layouts.site :seo="$seo">
    <x-site.page-hero :image="$months->flatten()->first()?->image" :title="__('site.nav.events')" :text="__('site.events.intro')" />

    <section class="bg-snow">
        <div class="container-site flex flex-col gap-10 md:gap-14 py-12 md:py-16 lg:py-20">
            @include('site.partials.hotel-filter', ['route' => 'events.index'])
            @forelse ($months as $ym => $events)
                <div>
                    <h2 class="eyebrow mb-4">{{ \Illuminate\Support\Carbon::createFromFormat('Y-m-d', $ym.'-01')->translatedFormat('F Y') }}</h2>
                    <div class="border-b border-mist">
                        @foreach ($events as $e)
                            @include('site.events.partials.row', ['event' => $e])
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="max-w-xl">
                    <p class="text-lg text-slate">{{ __('site.events.empty') }}</p>
                    <a href="{{ route('offers.index') }}" class="link-line mt-5">{{ __('site.nav.offers') }}</a>
                </div>
            @endforelse
        </div>
    </section>
</x-layouts.site>

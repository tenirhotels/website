<x-layouts.site :seo="$seo" :hotel="$hotel">
    <section class="bg-snow pt-10 md:pt-16">
        <div class="container-site">
            <p class="eyebrow">{{ __('gifts.eyebrow') }}</p>
            <h1 class="h-display mt-2 text-4xl text-forest md:text-6xl">{{ __('gifts.title') }}</h1>
            <p class="lead mt-4 max-w-2xl">{{ __('gifts.lead') }}</p>
        </div>
        <div class="container-site grid gap-x-8 gap-y-12 py-12 sm:grid-cols-2 md:py-16 lg:grid-cols-3">
            @forelse ($gifts as $g)
                @include('site.gifts.card', ['g' => $g])
            @empty
                <p class="text-lg text-slate sm:col-span-2 lg:col-span-3">{{ __('gifts.empty') }}</p>
            @endforelse
        </div>
        <div class="container-site pb-16 text-sm text-stone md:pb-24">{{ __('gifts.note') }}</div>
    </section>
</x-layouts.site>

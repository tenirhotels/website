<x-layouts.site :seo="$seo">
    <x-site.page-hero :image="$experiences->first()?->image" :title="__('site.nav.experiences')" :text="__('site.experiences.intro')" />

    <section class="container-site flex flex-col gap-12 py-16">
        @if ($categories->count() > 1)
            <nav aria-label="{{ __('site.nav.experiences') }}" class="flex flex-wrap gap-3">
                <a href="{{ route('experiences.index') }}" @class(['min-h-11 border px-5 py-2.5 text-[13px] uppercase tracking-[0.14em]', 'border-forest bg-forest text-white' => ! $active, 'border-mist hover:border-forest' => $active])>{{ __('site.common.all') }}</a>
                @foreach ($categories as $c)
                    <a href="{{ route('experiences.index', ['category' => $c]) }}"
                       @class(['min-h-11 border px-5 py-2.5 text-[13px] uppercase tracking-[0.14em]', 'border-forest bg-forest text-white' => $active === $c, 'border-mist hover:border-forest' => $active !== $c])>
                        {{ \App\Support\Locales::pick(config("tenir.experience_categories.$c")) }}
                    </a>
                @endforeach
            </nav>
        @endif

        <div class="grid gap-x-8 gap-y-16 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($experiences as $e)
                <x-site.card :href="$e->url()" :image="$e->image" :title="$e->t('title')"
                    :meta="collect([$e->hotel?->t('name'), $e->seasonLabels()])->filter()->implode(' · ')" :text="$e->t('summary')" />
            @empty
                <p class="text-slate">{{ __('site.common.empty') }}</p>
            @endforelse
        </div>
    </section>
</x-layouts.site>

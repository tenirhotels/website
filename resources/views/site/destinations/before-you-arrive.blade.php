@php use App\Models\Setting; @endphp
<x-layouts.site :seo="$seo">
    <x-site.page-hero :image="$hotel->arrivalImage()" :eyebrow="$hotel->t('name')" :title="__('site.hotel.before_arrive')" :text="$hotel->t('before_tease') ?: __('site.hotel.before_tease')" />

    @if ($hotel->t('arrival_info'))
        <section class="container-site flex flex-col gap-8 pb-12 pt-10 md:gap-14 md:py-16 lg:flex-row lg:gap-20 lg:py-20">
            <div class="flex flex-[1.5] flex-col gap-8">
                <div class="prose-tenir [&>*:first-child]:mt-0">{!! $hotel->t('arrival_info') !!}</div>
                <a href="{{ $hotel->sectionUrl('getting-here') }}" class="group flex flex-col gap-2 rounded-3xl border border-mist bg-sand p-6 transition hover:border-ember md:p-8">
                    <span class="font-display text-xl md:text-2xl">{{ __('site.hotel.getting_here') }}</span>
                    <span class="text-base leading-relaxed text-slate">{{ $hotel->t('getting_tease') ?: __('site.hotel.getting_tease') }}</span>
                    <span class="link-line mt-2 self-start">{{ __('site.hotel.getting_more') }}</span>
                </a>
            </div>
        </section>
    @endif

    <x-site.altitude-levels :hotel="$hotel" />

    @if ($faqs->isNotEmpty())
        <section class="container-site border-t border-mist py-12 md:py-16 lg:py-20">
            <h2 class="font-display text-2xl md:text-3xl">{{ __('site.hotel.faq_title') }}</h2>
            <div class="mt-8 divide-y divide-mist border-y border-mist">
                @foreach ($faqs as $faq)
                    <details class="group py-5">
                        <summary class="flex cursor-pointer list-none items-start justify-between gap-6 text-lg leading-snug text-ink [&::-webkit-details-marker]:hidden">
                            <span>{{ $faq->t('question') }}</span>
                            <span class="mt-1 shrink-0 text-ember transition group-open:rotate-45" aria-hidden="true">{{ site_icon('plus', 'h-5 w-5') }}</span>
                        </summary>
                        <div class="prose-tenir mt-4 max-w-3xl">{!! $faq->t('answer') !!}</div>
                    </details>
                @endforeach
            </div>
        </section>
    @endif

    <section class="bg-forest text-white">
        <div class="container-site flex flex-col gap-4 py-14 md:py-20">
            <p class="eyebrow-light">{{ Setting::text('brand.tagline') ?? 'Back to Nature' }}</p>
            <p class="max-w-3xl font-display text-2xl leading-snug md:text-3xl">{{ __('site.hotel.nature_1') }}</p>
            <p class="font-display text-xl text-sand md:text-2xl">{{ __('site.hotel.nature_2') }}</p>
        </div>
    </section>
</x-layouts.site>

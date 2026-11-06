@php($items = \App\Models\Recognition::forHome())
@if ($items->isNotEmpty())
    {{-- Награды и публикации: ведутся в админке «Сайт → Награды и СМИ»; только подтверждённые факты со ссылкой на источник. --}}
    <section class="bg-sand" aria-labelledby="recognition-title">
        <div class="container-site flex flex-col gap-10 py-16 md:py-20">
            <div class="flex flex-col gap-3">
                <p class="eyebrow">{{ __('site.recognition.eyebrow') }}</p>
                <h2 id="recognition-title" class="h-section">{{ __('site.recognition.title') }}</h2>
            </div>
            <ul @class(['grid gap-5', 'md:grid-cols-2' => $items->count() === 2, 'md:grid-cols-3' => $items->count() >= 3])>
                @foreach ($items as $item)
                    <li class="flex">
                        <{{ $item->url ? 'a' : 'div' }} @if ($item->url) href="{{ $item->url }}" target="_blank" rel="noopener" @endif
                            class="group flex w-full flex-col gap-4 rounded-3xl bg-white p-7 transition-shadow hover:shadow-[0_12px_32px_rgba(22,33,27,0.12)]">
                            <p class="text-xs uppercase tracking-[0.2em] text-ember">{{ collect([$item->source, $item->year])->filter()->implode(' · ') }}</p>
                            <p class="font-display text-[30px] leading-tight text-forest [font-variant-numeric:lining-nums]">{{ $item->t('title') }}</p>
                            @if ($sub = $item->t('subtitle'))
                                <p class="text-[15px] leading-relaxed text-slate">{{ $sub }}</p>
                            @endif
                            @if ($item->url)<span class="mt-auto text-[13px] uppercase tracking-[0.2em] text-stone group-hover:text-ember">{{ __('site.recognition.source') }} →</span>@endif
                        </{{ $item->url ? 'a' : 'div' }}>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>
@endif

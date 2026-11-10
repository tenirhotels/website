<x-filament-widgets::widget>
    <x-filament::section :heading="$problems ? 'Требует внимания: '.$problems : 'Что требует внимания'" icon="heroicon-o-clipboard-document-check" :icon-color="$problems ? 'warning' : 'success'">
        @if (! count($items))
            <p class="text-sm text-gray-600 dark:text-gray-300">Всё в порядке: фото, контакты, номера и переводы заполнены, истёкших предложений нет.</p>
        @else
            <ul class="divide-y divide-gray-100 dark:divide-white/10">
                @foreach ($items as $i)
                    <li class="flex items-start gap-3 py-3">
                        <x-filament::icon :icon="$i['level'] === 'warn' ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-information-circle'" @class(['mt-0.5 h-5 w-5 shrink-0', 'text-warning-500' => $i['level'] === 'warn', 'text-gray-400' => $i['level'] !== 'warn']) />
                        <span class="flex-1 text-sm text-gray-800 dark:text-gray-100">{{ $i['text'] }}</span>
                        @if ($i['url'])
                            <a href="{{ $i['url'] }}" class="shrink-0 text-sm font-medium text-primary-600 hover:underline dark:text-primary-400">Открыть →</a>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>

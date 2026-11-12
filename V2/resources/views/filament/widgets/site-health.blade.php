<x-filament-widgets::widget>
    <x-filament::section :heading="$problems ? 'Требует внимания: '.$problems : 'Что требует внимания'" icon="heroicon-o-clipboard-document-check" :icon-color="$problems ? 'warning' : 'success'">
        @if (! count($items))
            <p style="font-size:14px;margin:0">Всё в порядке: фото, контакты, номера и переводы заполнены, истёкших предложений нет.</p>
        @else
            <ul style="list-style:none;margin:0;padding:0">
                @foreach ($items as $i)
                    <li style="display:flex;align-items:flex-start;gap:12px;padding:12px 0;{{ ! $loop->first ? 'border-top:1px solid rgba(128,128,128,.2);' : '' }}">
                        <x-filament::icon :icon="$i['level'] === 'warn' ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-information-circle'"
                            style="width:20px;height:20px;flex-shrink:0;margin-top:1px;color:{{ $i['level'] === 'warn' ? 'var(--warning-500, #f59e0b)' : 'var(--gray-400, #9ca3af)' }}" />
                        <span style="flex:1;min-width:0;font-size:14px;line-height:1.5">{{ $i['text'] }}</span>
                        @if ($i['url'])
                            <a href="{{ $i['url'] }}" style="flex-shrink:0;font-size:14px;font-weight:500;white-space:nowrap;color:var(--primary-600, #059669)">Открыть →</a>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>

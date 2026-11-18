<x-filament-widgets::widget>
    <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px 16px">
        {{-- Создать: одна кнопка, что именно — выбирается в списке --}}
        <x-filament::dropdown placement="bottom-start" teleport>
            <x-slot name="trigger">
                <x-filament::button icon="heroicon-m-plus" icon-position="before" color="primary">
                    Создать
                </x-filament::button>
            </x-slot>
            <x-filament::dropdown.list>
                @foreach ($create as [$label, $url, $icon])
                    <x-filament::dropdown.list.item tag="a" :href="$url" :icon="$icon">{{ $label }}</x-filament::dropdown.list.item>
                @endforeach
            </x-filament::dropdown.list>
        </x-filament::dropdown>

        <div style="display:flex;flex-wrap:wrap;align-items:center;gap:12px">
            <x-filament::button tag="a" :href="$settingsUrl" icon="heroicon-o-cog-6-tooth" color="gray" outlined>Настройки сайта</x-filament::button>
            <x-filament::button tag="a" :href="$siteUrl" target="_blank" icon="heroicon-o-arrow-top-right-on-square" color="gray" outlined>Открыть сайт</x-filament::button>
        </div>
    </div>
</x-filament-widgets::widget>

<x-filament-widgets::widget>
    <div class="flex flex-wrap gap-3">
        @foreach ($actions as [$label, $url, $icon, $external])
            <x-filament::button tag="a" :href="$url" :icon="$icon" :color="$external ? 'primary' : 'gray'" :outlined="! $external" :target="$external ? '_blank' : null">
                {{ $label }}
            </x-filament::button>
        @endforeach
    </div>
</x-filament-widgets::widget>

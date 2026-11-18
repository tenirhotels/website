<x-filament-panels::page>
    @vite(['resources/js/calibrate.js'])

    @php($locations = $this->locations())

    @if ($locations->isEmpty())
        <p>Нет ни одной локации с картой.</p>
    @else
        @if ($locations->count() > 1)
            <select wire:model.live="locationId" class="fi-input rounded-lg border px-3 py-2" style="max-width:24rem">
                @foreach ($locations as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        @endif

        <div wire:key="cal-{{ $locationId }}" wire:ignore
             x-data="{ cfg: @js($this->config()) }"
             x-init="(function w(){ window.TenirCalibrate ? TenirCalibrate.init($el, cfg, (a, i) => $wire.save(a, i)) : setTimeout(w, 60); })()"
             class="cal">
            <div class="cal-hint" data-hint></div>

            <div class="cal-bar">
                <button type="button" class="is-primary" data-save disabled>Сохранить</button>
                <button type="button" data-undo>Отменить последнюю точку</button>
                <label><input type="checkbox" data-overlay> Показать иллюстрацию поверх снимка</label>
                <label>Прозрачность <input type="range" min="10" max="100" value="60" data-opacity></label>
                <span data-overlay-note style="color:#6b6a63"></span>
            </div>

            <div class="cal-panes">
                <div class="cal-pane"><span class="cal-title">Иллюстрация</span><div data-img></div></div>
                <div class="cal-pane"><span class="cal-title">Местность (спутник)</span><div data-geo></div></div>
            </div>

            <p class="cal-sum" data-sum></p>

            <table class="cal-table">
                <thead><tr><th>№</th><th>Точка</th><th title="Насколько точка расходится с подгонкой, м">Ошибка, м</th><th title="Ошибка, если эту точку убрать из подгонки: показывает, насколько можно доверять карте между опорными точками">Без этой точки, м</th><th></th></tr></thead>
                <tbody data-rows></tbody>
            </table>
            <p style="font-size:13px;color:#6b6a63">Синие — метки карты с координатами (снимите галочку, если иконка нарисована не в настоящем месте), оранжевые — ваши точки. «Без этой точки» — честная оценка точности: чем меньше, тем лучше карта держит место между опорными точками (до 30 м — зелёный, до 100 — жёлтый).</p>
        </div>
    @endif
</x-filament-panels::page>

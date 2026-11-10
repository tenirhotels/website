{{-- Выбор точки на карте локации: нажатие на картинку записывает x и y (в пикселях иллюстрации) в соседние поля. --}}
@php
    $base = \Illuminate\Support\Str::beforeLast($getStatePath(), '.');
@endphp
<div class="fi-map-point" wire:ignore.self
     x-data="{
        x: $wire.$entangle(@js($base.'.x')),
        y: $wire.$entangle(@js($base.'.y')),
        W: {{ (int) $width }}, H: {{ (int) $height }},
        open: false, zoom: 1,
        pick(e) {
            const r = this.$refs.img.getBoundingClientRect();
            this.x = Math.max(0, Math.min(this.W, Math.round((e.clientX - r.left) / r.width * this.W)));
            this.y = Math.max(0, Math.min(this.H, Math.round((e.clientY - r.top) / r.height * this.H)));
        },
        setZoom(z) {
            const box = this.$refs.box, cx = (box.scrollLeft + box.clientWidth / 2) / box.scrollWidth, cy = (box.scrollTop + box.clientHeight / 2) / box.scrollHeight;
            this.zoom = Math.max(1, Math.min(5, z));
            this.$nextTick(() => { box.scrollLeft = cx * box.scrollWidth - box.clientWidth / 2; box.scrollTop = cy * box.scrollHeight - box.clientHeight / 2; });
        },
        show() {
            this.open = true;
            if (this.x && this.y) { this.zoom = 2.5; this.$nextTick(() => { const b = this.$refs.box; b.scrollLeft = this.x / this.W * b.scrollWidth - b.clientWidth / 2; b.scrollTop = this.y / this.H * b.scrollHeight - b.clientHeight / 2; }); }
        },
     }">
    @if ($image)
        <div style="display:flex;flex-wrap:wrap;align-items:center;gap:.75rem">
            <button type="button" x-show="!open" x-on:click="show()"
                    style="padding:.5rem 1rem;border-radius:.5rem;background:#1F3A2E;color:#fff;font-size:.875rem;font-weight:600">Указать на карте</button>
            <template x-if="open">
                <div style="display:flex;align-items:center;gap:.5rem;font-size:.875rem">
                    <button type="button" x-on:click="setZoom(zoom - .75)" style="width:2.25rem;height:2.25rem;border:1px solid #d4d4d8;border-radius:.5rem;font-size:1.1rem">−</button>
                    <button type="button" x-on:click="setZoom(zoom + .75)" style="width:2.25rem;height:2.25rem;border:1px solid #d4d4d8;border-radius:.5rem;font-size:1.1rem">+</button>
                    <button type="button" x-on:click="open = false" style="padding:.45rem .9rem;border:1px solid #d4d4d8;border-radius:.5rem">Свернуть карту</button>
                </div>
            </template>
            <span style="font-size:.8rem;color:#71717a" x-show="open">Нажмите на карту — координаты x и y заполнятся сами. Кнопки + и − приближают.</span>
        </div>
        <template x-if="open">
            <div x-ref="box" style="margin-top:.75rem;max-height:70vh;overflow:auto;border:1px solid #e4e4e7;border-radius:.75rem;background:#dce9f6;-webkit-overflow-scrolling:touch">
                <div style="position:relative" x-bind:style="'width:' + (zoom * 100) + '%'">
                    <img x-ref="img" src="{{ $image }}" alt="" draggable="false" x-on:click="pick($event)"
                         style="display:block;width:100%;height:auto;cursor:crosshair;user-select:none">
                    @foreach ($others as $o)
                        <span title="{{ $o['label'] }}" style="position:absolute;left:{{ round($o['x'] / max(1, $width) * 100, 3) }}%;top:{{ round($o['y'] / max(1, $height) * 100, 3) }}%;width:10px;height:10px;margin:-5px 0 0 -5px;border-radius:50%;background:rgba(255,255,255,.85);border:2px solid rgba(31,58,46,.7);pointer-events:none"></span>
                    @endforeach
                    <span x-show="x && y" x-bind:style="'left:' + (x / W * 100) + '%;top:' + (y / H * 100) + '%'"
                          style="position:absolute;width:22px;height:22px;margin:-11px 0 0 -11px;border-radius:50%;background:#B5561F;border:3px solid #fff;box-shadow:0 1px 6px rgba(0,0,0,.5);pointer-events:none"></span>
                </div>
            </div>
        </template>
    @else
        <p style="font-size:.875rem;color:#71717a">Чтобы выбирать точку нажатием, сначала загрузите картинку карты (раздел «Иллюстрации») и сохраните локацию.</p>
    @endif
</div>

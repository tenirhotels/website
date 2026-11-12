<?php

namespace App\Filament\Pages;

use App\Models\Location;
use App\Support\LocationMap;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * «Привязка карты»: иллюстрация рядом со спутниковым снимком. Пары точек (место на рисунке ↔ место на местности)
 * сохраняются в locations.map.geo_points и используются кнопкой «Вы здесь» на сайте.
 */
class MapCalibration extends Page
{
    use \App\Filament\Support\AdminOnly;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static string|UnitEnum|null $navigationGroup = 'Отели';

    protected static ?string $navigationLabel = 'Привязка карты';

    protected static ?string $title = 'Привязка карты к местности';

    protected static ?int $navigationSort = 50;

    protected string $view = 'filament.pages.map-calibration';

    public ?int $locationId = null;

    public function mount(): void
    {
        $this->locationId = $this->locations()->keys()->first();
    }

    /** Локации, у которых есть карта. @return \Illuminate\Support\Collection<int, string> */
    public function locations(): \Illuminate\Support\Collection
    {
        return Location::query()->orderBy('id')->get()
            ->filter(fn (Location $l) => LocationMap::for($l) !== null)
            ->mapWithKeys(fn (Location $l) => [$l->id => (string) ($l->t('name') ?: 'Локация #'.$l->id)]);
    }

    /** Всё, что нужно странице: картинка, метки с координатами, текущие точки. */
    public function config(): array
    {
        $loc = $this->locationId ? Location::find($this->locationId) : null;
        $map = LocationMap::for($loc);
        if (! $map) {
            return [];
        }
        $raw = (array) $loc->map;

        return [
            'image' => $map->image('winter') ?: $map->image('summer'),
            'width' => $map->width(),
            'height' => $map->height(),
            'pins' => $map->pins()->filter(fn ($p) => filled($p['lat'] ?? null) && filled($p['lng'] ?? null))
                ->map(fn ($p) => ['id' => (string) $p['id'], 'label' => (string) $p['label'], 'lat' => (float) $p['lat'], 'lng' => (float) $p['lng'], 'x' => (int) $p['x'], 'y' => (int) $p['y']])
                ->values()->all(),
            'anchors' => array_values((array) ($raw['geo_points'] ?? [])),
            'ignore' => array_values((array) ($raw['geo_ignore'] ?? [])),
        ];
    }

    /**
     * Сохранить точки и список отключённых меток.
     *
     * @param  list<array{lat: float|int|string, lng: float|int|string, x: float|int|string, y: float|int|string}>  $anchors
     * @param  list<string>  $ignore
     */
    public function save(array $anchors, array $ignore): void
    {
        $loc = Location::findOrFail($this->locationId);
        $map = LocationMap::for($loc);
        abort_unless($map, 404);

        $clean = collect($anchors)->take(500)
            ->filter(fn ($a) => is_array($a) && is_numeric($a['lat'] ?? null) && is_numeric($a['lng'] ?? null) && is_numeric($a['x'] ?? null) && is_numeric($a['y'] ?? null))
            ->map(fn ($a) => [
                'lat' => round(max(-90, min(90, (float) $a['lat'])), 6),
                'lng' => round(max(-180, min(180, (float) $a['lng'])), 6),
                'x' => (int) max(0, min($map->width(), round((float) $a['x']))),
                'y' => (int) max(0, min($map->height(), round((float) $a['y']))),
            ])->values()->all();

        $known = collect((array) ($loc->map['pins'] ?? []))->pluck('id')->map(fn ($i) => (string) $i)->all();
        $off = collect($ignore)->map(fn ($i) => (string) $i)->filter(fn ($i) => in_array($i, $known, true))->unique()->values()->all();

        $data = (array) $loc->map;
        $data['geo_points'] = $clean;
        $data['geo_ignore'] = $off;
        $loc->map = $data;
        $loc->save();

        Notification::make()->title('Сохранено')->body('Точек привязки: '.count($clean).'. Кэш страниц сброшен.')->success()->send();
    }
}

<?php

use App\Models\Location;
use App\Models\Venue;
use Illuminate\Database\Migrations\Migration;

/**
 * Метки ресторанов на карте получают venue_id. Автопривязка только там, где совпадение однозначное:
 * название метки (на любом языке) равно названию ресторана отелей этой локации. Остальное привязывается в админке.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (Location::query()->get() as $location) {
            $map = $location->map;
            if (! is_array($map) || empty($map['pins'])) {
                continue;
            }
            $venues = Venue::query()->whereHas('hotel', fn ($q) => $q->where('location_id', $location->id))->get();
            $changed = false;
            foreach ($map['pins'] as &$pin) {
                if (($pin['kind'] ?? '') !== 'restaurant' || filled($pin['venue_id'] ?? null)) {
                    continue;
                }
                $labels = collect((array) ($pin['label'] ?? []))->filter()->map(fn ($l) => mb_strtolower(trim($l)))->all();
                $hits = $venues->filter(fn ($v) => collect((array) ($v->name ?? []))->filter()->map(fn ($n) => mb_strtolower(trim($n)))->intersect($labels)->isNotEmpty());
                if ($hits->count() === 1) {
                    $pin['venue_id'] = $hits->first()->id;
                    $changed = true;
                }
            }
            unset($pin);
            if ($changed) {
                $location->map = $map;
                $location->save();
            }
        }
    }

    public function down(): void
    {
    }
};

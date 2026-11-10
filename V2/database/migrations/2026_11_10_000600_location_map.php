<?php

use App\Support\LocationMap;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * Карта курорта — часть локации: иллюстрация, станции, метки, время в пути, GPS-привязка, панорамы.
 * Локациям с канатной дорогой переносим карту Шымбулака (раньше она жила в коде).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->json('map')->nullable()->after('longitude');
        });

        $ids = DB::table('transports')->where('kind', 'cable_line')->distinct()->pluck('location_id');
        $ids = $ids->isEmpty() ? DB::table('locations')->where('slug', 'shymbulak')->pluck('id') : $ids;
        foreach ($ids as $id) {
            $slug = (string) DB::table('locations')->where('id', $id)->value('slug');
            $map = LocationMap::shymbulakDefaults();
            $map['images'] = $this->copyImages($map['images'], $slug);
            DB::table('locations')->where('id', $id)->update(['map' => json_encode($map, JSON_UNESCAPED_UNICODE)]);
        }
    }

    /** Картинки карты — в хранилище, чтобы их можно было заменить в админке (поле загрузки видит только диск public). */
    private function copyImages(array $images, string $slug): array
    {
        foreach ($images as $key => $url) {
            $src = public_path(ltrim($url, '/'));
            if (! is_file($src)) {
                continue;
            }
            try {
                $to = "locations/map/$slug-".basename($src);
                Storage::disk('public')->put($to, File::get($src));
                $images[$key] = $to;
            } catch (\Throwable) {
                // остаётся адрес из public/images — карта работает, заменить её можно будет загрузкой
            }
        }

        return $images;
    }

    public function down(): void
    {
        Schema::table('locations', fn (Blueprint $table) => $table->dropColumn('map'));
    }
};

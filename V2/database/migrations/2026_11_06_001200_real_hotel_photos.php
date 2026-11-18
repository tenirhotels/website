<?php

use App\Models\Hotel;
use App\Models\Page;
use App\Models\RoomType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Storage;

/**
 * Настоящие фото отеля (съёмка 2021–2022, архив OneDrive) вместо временных картинок из V1.
 * Меняем только то, что всё ещё указывает на старые временные файлы — загруженное в админке не трогаем.
 */
return new class extends Migration
{
    private const FILES = ['tenir-modules', 'tenir-reception', 'tenir-stilts', 'tenir-glacier', 'tenir-logo', 'tenir-glacier-portrait', 'tenir-logo-portrait'];

    public function up(): void
    {
        foreach (self::FILES as $name) {
            $source = database_path("seeders/media/$name.webp");
            if (is_file($source) && ! Storage::disk('public')->exists("seed/$name.webp")) {
                Storage::disk('public')->put("seed/$name.webp", file_get_contents($source));
            }
        }
        $p = fn (string $n) => "seed/$n.webp";

        foreach (Hotel::all() as $hotel) {
            if ($hotel->hero_image === 'seed/hero.webp') {
                $hotel->hero_image = $p('tenir-modules');
            }
            $old = ['seed/about.webp', 'seed/story-1.webp', 'seed/hotel-1.webp', 'seed/hero-back.webp', 'seed/philosophy.webp'];
            if ((array) $hotel->gallery === $old) {
                $hotel->gallery = array_map($p, ['tenir-reception', 'tenir-modules', 'tenir-stilts', 'tenir-glacier', 'tenir-logo']) + [5 => 'seed/hotel-1.webp'];
            }
            $hotel->save();
        }

        foreach (RoomType::all() as $room) {
            if ($room->image === 'seed/story-1.webp') {
                $room->image = $p('tenir-stilts');
            }
            if ((array) $room->gallery === ['seed/about.webp', 'seed/hero-back.webp']) {
                $room->gallery = [$p('tenir-modules'), $p('tenir-glacier')];
            }
            $room->save();
        }

        $about = Page::where('slug', 'about')->first();
        if ($about) {
            if ($about->hero_image === 'seed/philosophy.webp') {
                $about->hero_image = $p('tenir-glacier');
            }
            $about->content = $this->swapBlockImages((array) $about->content, [
                'seed/about.webp' => $p('tenir-logo-portrait'),
                'seed/exp-4.webp' => $p('tenir-glacier-portrait'),
            ]);
            $about->save();
        }

        $sus = Page::where('slug', 'sustainability')->first();
        if ($sus && $sus->hero_image === 'seed/hero-back.webp') {
            $sus->hero_image = $p('tenir-stilts');
            $sus->save();
        }
    }

    private function swapBlockImages(array $content, array $map): array
    {
        foreach ($content as $locale => $blocks) {
            foreach ((array) $blocks as $i => $block) {
                $img = $block['data']['image'] ?? null;
                if ($img && isset($map[$img])) {
                    $content[$locale][$i]['data']['image'] = $map[$img];
                }
            }
        }

        return $content;
    }

    public function down(): void
    {
        //
    }
};

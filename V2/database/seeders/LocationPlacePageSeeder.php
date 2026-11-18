<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

/**
 * Черновик страницы /place/shymbulak: заполняет только пустые поля, ничего не перезаписывает.
 * Страница остаётся ВЫКЛЮЧЕННОЙ — включается в админке (Локации → Страница локации) после проверки текста.
 */
class LocationPlacePageSeeder extends Seeder
{
    public function run(): void
    {
        $loc = Location::where('slug', 'shymbulak')->first();
        if (! $loc) {
            return;
        }

        $loc->lead = $loc->lead ?: [
            'ru' => 'Горный курорт в Заилийском Алатау над Алматы: канатная дорога, лыжные трассы и ледники на высоте до 3 200 метров.',
            'en' => 'A mountain resort in the Trans-Ili Alatau above Almaty: cable cars, ski runs and glaciers at up to 3,200 metres.',
        ];
        $loc->about = $loc->about ?: [
            'ru' => '<p>Шымбулак — горный курорт в Заилийском Алатау, над Алматы. Сюда поднимаются по канатной дороге от Медеу, а выше — к верхним станциям и ледникам.</p><p>Tenir расположен на высоте 3 200 метров — это самый высокогорный отель Казахстана. Зимой здесь катаются на лыжах и сноуборде, летом ходят в горы и любуются ледниками.</p>',
            'en' => '<p>Shymbulak is a mountain resort in the Trans-Ili Alatau, above Almaty. It is reached by cable car from Medeu, and higher still to the upper stations and glaciers.</p><p>Tenir stands at 3,200 metres, the highest-altitude hotel in Kazakhstan. In winter guests ski and snowboard; in summer they hike and admire the glaciers.</p>',
        ];

        $place = (array) $loc->place;
        $place += ['enabled' => false];
        $place['weather'] = $place['weather'] ?? ['enabled' => true];
        $loc->place = $place;
        $loc->save();
    }
}

<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        Setting::put('brand', [
            'name' => ['ru' => 'Tenir Hotels', 'en' => 'Tenir Hotels', 'kk' => 'Tenir Hotels', 'zh' => 'Tenir Hotels'],
            'tagline' => ['ru' => 'Назад к природе', 'en' => 'Back to Nature', 'kk' => 'Табиғатқа оралу', 'zh' => '回归自然'],
            'footer_text' => [
                'ru' => 'Эко-отели в горах Казахстана. Тишина, звёзды и снег — у вас за окном.',
                'en' => 'Eco hotels in the mountains of Kazakhstan. Silence, stars and snow outside your window.',
            ],
            'newsletter_heading' => ['ru' => 'Новые места Tenir', 'en' => 'New Tenir places'],
            'legal_name' => 'ТОО «Alpine Hotel»',
        ]);

        Setting::put('contacts', [
            'phone' => '+7 727 313 14 15',
            'whatsapp' => '+7 727 313 14 15',
            'email' => 'reservations@tenirhotels.com',
            'hours' => '09:00–21:00',
        ]);

        Setting::put('social', [
            'instagram' => 'https://www.instagram.com/tenir.ecohotels',
            'facebook' => 'https://facebook.com/tenirhotels',
            'youtube' => 'https://youtube.com/@tenirhotels',
        ]);

        // Счётчики из V1 — проверьте, актуальны ли.
        Setting::put('analytics', [
            'ga4' => 'G-5QTC3FKSJV',
            'yandex_metrika' => '69916861',
            'gtm' => null,
        ]);

        Setting::put('seo', [
            'default_title' => [
                'ru' => 'Tenir Hotels — эко-отель на высоте 3 200 м, Шымбулак',
                'en' => 'Tenir Hotels — eco hotel at 3,200 m, Shymbulak',
                'kk' => 'Tenir Hotels — 3 200 м биіктіктегі эко-қонақүй, Шымбұлақ',
            ],
            'default_description' => [
                'ru' => 'Самый высокогорный эко-отель Казахстана на курорте Шымбулак. Десять отдельных модулей, виды на ледники и облака, ski-in / ski-out.',
                'en' => 'The highest-altitude eco hotel in Kazakhstan at Shymbulak resort. Ten private modules, views of glaciers and clouds, ski-in / ski-out.',
            ],
            'og_image' => null,
        ]);
    }
}

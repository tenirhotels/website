<?php

namespace Database\Seeders;

use App\Models\Page;
use Database\Seeders\Concerns\CopiesMedia;
use Database\Seeders\Concerns\PhilosophyContent;
use Database\Seeders\Concerns\SustainabilityContent;
use Illuminate\Database\Seeder;

/**
 * Страницы бренда: «О бренде» (философия Тәңір) и Sustainability. Тексты — с текущего сайта.
 */
class BrandPagesSeeder extends Seeder
{
    use CopiesMedia;

    public function run(): void
    {
        Page::updateOrCreate(['slug' => 'about'], [
            'title' => ['ru' => 'Наша философия', 'en' => 'Our philosophy', 'kk' => 'Біздің философия', 'zh' => '我们的理念'],
            'summary' => [
                'ru' => 'Вновь почувствовать природу и свою связь с ней.',
                'en' => 'To reconnect with nature and feel part of it again.',
            ],
            'hero_image' => $this->media('philosophy.webp'),
            'placement' => 'main',
            'sort' => 1,
            'content' => [
                'ru' => [
                    ['type' => 'word', 'data' => [
                        'word' => 'Тәңір',
                        'meaning' => 'по-казахски — «небо»',
                        'text' => 'Мы лишь малая часть огромного, прекрасного и гармоничного мира. Tenir — место, чтобы снова это почувствовать.',
                    ]],
                    ['type' => 'image_text', 'data' => [
                        'eyebrow' => 'Философия',
                        'heading' => 'Природа как живое, священное присутствие',
                        'body' => '<p>Философия Tenir — дать гостям возможность вновь соединиться с природой, почувствовать её присутствие и осознать, что мы лишь малая часть огромного, прекрасного и гармоничного мира.</p><p>Здесь можно смотреть на звёздное небо, слушать тишину белого снега и шёпот ветра или просто погрузиться в покой величественных вершин Заилийского Алатау — обретая гармонию и силу в природной энергии гор.</p>',
                        'image' => $this->media('about.webp'),
                        'image_side' => 'right',
                    ]],
                    ['type' => 'text', 'data' => [
                        'heading' => 'Происхождение имени',
                        'body' => '<p>Имя Tenir восходит к Тенгри — древнему божеству неба в тенгрианской традиции. По-казахски «Тәңір» означает «небо». Тенгрианство выросло из мировоззрения тюркских кочевых племён и было тесно связано с восприятием природы как живого, священного присутствия.</p><p>С древних времён эта вера сближала людей с природой: кочевники считали себя её частью и жили в гармонии с окружающим миром. Верховным божеством, давшим вере имя, был Тенгри — воплощение вечного неба.</p>',
                    ]],
                    ['type' => 'cta', 'data' => [
                        'heading' => 'Бережно к горам',
                        'text' => 'Как мы заботимся о природе: от материалов здания до рефиллов в ванных комнатах.',
                        'button_label' => 'Устойчивое развитие',
                        'button_url' => 'page:sustainability',
                    ]],
                    ['type' => 'cta', 'data' => [
                        'heading' => 'Ближе к небу',
                        'text' => 'Десять модулей на высоте 3 200 м ждут вас.',
                        'button_label' => 'Забронировать',
                        'button_url' => 'booking',
                    ]],
                ],
                'en' => [
                    ['type' => 'word', 'data' => [
                        'word' => 'Тәңір',
                        'meaning' => '“sky” in Kazakh',
                        'text' => 'We are only a small part of a vast, beautiful and harmonious world. Tenir is a place to feel it again.',
                    ]],
                    ['type' => 'image_text', 'data' => [
                        'eyebrow' => 'Philosophy',
                        'heading' => 'Nature as a living, sacred presence',
                        'body' => '<p>The philosophy of Tenir is to offer guests the chance to reconnect with nature, to feel its presence, and to realise that we are only a small part of a vast, beautiful and harmonious world.</p><p>Here you can gaze at the star-filled sky, hear the silence of white snow and the whisper of the wind, or simply immerse yourself in the stillness of the Trans-Ili Alatau peaks — finding peace and renewal in the natural energy of the mountains.</p>',
                        'image' => $this->media('about.webp'),
                        'image_side' => 'right',
                    ]],
                    ['type' => 'text', 'data' => [
                        'heading' => 'Origin of the name',
                        'body' => '<p>The name Tenir comes from Tengri, the ancient sky deity of the Tengrian tradition. In Kazakh, “Тәңір” means “sky”. Tengrism grew out of the worldview of Turkic nomadic tribes and was closely tied to seeing nature as a living, sacred presence.</p><p>Since ancient times this belief brought people closer to nature: nomads saw themselves as part of it and lived in harmony with the world around them. The supreme deity who gave the belief its name was Tengri — the embodiment of the eternal sky.</p>',
                    ]],
                    ['type' => 'cta', 'data' => [
                        'heading' => 'Caring for the mountains',
                        'text' => 'How we look after nature — from the building materials to refillable dispensers in the bathrooms.',
                        'button_label' => 'Sustainability',
                        'button_url' => 'page:sustainability',
                    ]],
                    ['type' => 'cta', 'data' => [
                        'heading' => 'Closer to the sky',
                        'text' => 'Ten modules at 3,200 m are waiting for you.',
                        'button_label' => 'Book now',
                        'button_url' => 'booking',
                    ]],
                ],
                'kk' => PhilosophyContent::kk($this->media('about.webp')),
            ],
            'is_published' => true,
        ]);

        Page::updateOrCreate(['slug' => 'sustainability'], [
            'title' => ['ru' => 'Устойчивое развитие', 'en' => 'Sustainability', 'kk' => 'Тұрақты даму', 'zh' => '可持续发展'],
            'summary' => SustainabilityContent::summary(),
            'hero_image' => $this->media('hero-back.webp'),
            'placement' => 'brand',
            'sort' => 2,
            'content' => SustainabilityContent::content(),
            'is_published' => true,
        ]);

        foreach ([
            'privacy' => ['ru' => 'Политика конфиденциальности', 'en' => 'Privacy policy'],
            'terms' => ['ru' => 'Условия использования', 'en' => 'Terms of use'],
        ] as $slug => $title) {
            Page::updateOrCreate(['slug' => $slug], [
                'title' => $title,
                'placement' => 'footer',
                'sort' => 10,
                'content' => [
                    'ru' => [['type' => 'text', 'data' => ['heading' => null, 'body' => '<p>[Вставьте текст из текущего PDF-документа.]</p>']]],
                    'en' => [['type' => 'text', 'data' => ['heading' => null, 'body' => '<p>[Paste the text from the current PDF document.]</p>']]],
                ],
                'is_published' => true,
            ]);
        }
    }
}

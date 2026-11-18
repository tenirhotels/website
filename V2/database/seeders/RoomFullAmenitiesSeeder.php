<?php

namespace Database\Seeders;

use App\Models\RoomType;
use Illuminate\Database\Seeder;

/**
 * Полный список удобств Deluxe по группам (для страницы номера). Выполняется один раз: если у пунктов уже есть группы — ничего не трогает.
 * Сохраняет параметры (площадь, гости, кровать, вид) и пункты, отмеченные «Выделить».
 */
class RoomFullAmenitiesSeeder extends Seeder
{
    private const LIST = [
        'video' => [['TV set', 'Телевизор'], ['Music system', 'Музыкальная система'], ['Netflix Premium', 'Netflix Premium']],
        'internet' => [['Wi-Fi', 'Wi-Fi'], ['Phone', 'Телефон'], ['Internet', 'Интернет']],
        'devices' => [['Air conditioning', 'Кондиционер'], ['Hair dryer', 'Фен'], ['Lamp', 'Светильник'], ['Heater', 'Обогреватель'], ['Climate control system', 'Система климат-контроля'], ['Drying cabinet', 'Сушильный шкаф'], ['Iron', 'Утюг'], ['Coffee machine', 'Кофемашина']],
        'bathroom' => [['Shower', 'Душ'], ['Heated floor', 'Тёплый пол'], ['Toiletries', 'Косметические принадлежности'], ['Bath towels', 'Банные полотенца'], ['Hygiene kit', 'Гигиенический набор'], ['Sink', 'Раковина'], ['Sauna', 'Сауна'], ['Slippers', 'Тапочки'], ['Toilet', 'Туалет'], ['Bathrobe', 'Халат']],
        'outdoor' => [['Mountain view', 'Вид на горы'], ['Patio', 'Патио'], ['Panoramic windows', 'Панорамные окна'], ['Ski-in / ski-out', 'Ski-in / ski-out']],
        'furniture' => [['Armchair', 'Кресло'], ['Folding sofa', 'Раскладной диван'], ['Table', 'Стол'], ['Clothes rack', 'Вешалка для одежды'], ['Coffee table', 'Журнальный столик'], ['Pouf', 'Пуф'], ['Wardrobe', 'Шкаф'], ['Chest of drawers', 'Комод']],
        'other' => [['Safe', 'Сейф'], ['Kettle', 'Чайник'], ['Tea set', 'Чайный набор'], ['Non-smoking room', 'Номер для некурящих'], ['Drinking water', 'Питьевая вода'], ['Mineral water', 'Минеральная вода'], ['Minibar', 'Мини-бар'], ['Carpeting', 'Ковровое покрытие'], ['Room service', 'Обслуживание в номер'], ['Soundproof room', 'Звукоизоляция'], ['Breakfast in the room', 'Завтрак в номер'], ['Detached module', 'Отдельный модуль'], ['Locker for sports equipment', 'Локер для спортивного инвентаря']],
    ];

    public function run(): void
    {
        $room = RoomType::where('slug', 'deluxe-mountain-view')->first();
        if (! $room) {
            return;
        }
        $current = is_array($room->features) && $room->features !== [] ? $room->features : $room->defaultFeatures();
        if (collect($current)->contains(fn ($f) => filled($f['group'] ?? null))) {
            return;
        }

        $facts = collect($current)->filter(fn ($f) => ($f['source'] ?? 'custom') !== 'custom')->values()->all();
        $pinned = collect($current)->filter(fn ($f) => ($f['source'] ?? 'custom') === 'custom' && ($f['pin'] ?? false))
            ->map(fn ($f) => $f + ['group' => amenity_group($f['icon'] ?? 'leaf')])->values()->all();
        $pinnedKeys = collect($pinned)->map(fn ($f) => $this->key($f['text'] ?? []))->filter()->all();

        $items = [];
        foreach (self::LIST as $group => $rows) {
            foreach ($rows as [$en, $ru]) {
                $text = ['ru' => $ru, 'en' => $en];
                if (in_array($this->key($text), $pinnedKeys, true)) {
                    continue;
                }
                $items[] = ['source' => 'custom', 'icon' => amenity_icon_name($en), 'pin' => false, 'group' => $group, 'text' => $text];
            }
        }

        $room->features = array_merge($facts, $pinned, $items);
        $room->save();
    }

    /** Грубое сравнение «та же вещь»: сауна ↔ Sauna ↔ Private Finnish sauna. */
    private function key(array $text): ?string
    {
        $t = mb_strtolower(($text['en'] ?? '').' '.($text['ru'] ?? ''));

        return match (true) {
            str_contains($t, 'sauna') || str_contains($t, 'саун') => 'sauna',
            default => null,
        };
    }
}

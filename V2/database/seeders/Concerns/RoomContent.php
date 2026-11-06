<?php

namespace Database\Seeders\Concerns;

/** Удобства номера Deluxe — по описанию на текущем сайте (V1). */
class RoomContent
{
    public const OLD_AMENITIES_RU = ['Финская сауна', 'Панорамное окно', 'Отдельный модуль', 'Ski-in / ski-out'];

    public static function amenities(string $locale): array
    {
        return match ($locale) {
            'en' => [
                'Private Finnish sauna', 'Panoramic window', 'Detached module', 'Ski-in / ski-out',
                'King-size bed', 'Free Wi-Fi', 'TV with satellite channels', 'Minibar',
                'Coffee machine and kettle', 'Breakfast in the room', 'Shower and toiletries', 'Robe, slippers, hairdryer',
                'Locker for sports equipment',
            ],
            default => [
                'Финская сауна в номере', 'Панорамное окно', 'Отдельный модуль', 'Ski-in / ski-out',
                'Кровать king-size', 'Бесплатный Wi-Fi', 'Телевизор, спутниковые каналы', 'Мини-бар',
                'Кофеварка и чайник', 'Завтрак в номер', 'Душ и косметические принадлежности', 'Халат, тапочки, фен',
                'Локер для спортивного инвентаря',
            ],
        };
    }

    public static function description(string $locale): string
    {
        return match ($locale) {
            'en' => '<p>Stylish, comfortable two-person modular houses with room for an extra bed for children from 8 years old. The room opens onto magnificent views of the Trans-Ili Alatau mountains.</p>',
            default => '<p>Стильные комфортабельные двухместные модульные дома с возможностью размещения дополнительной кровати для детей с 8 лет. Из номера открывается великолепный вид на горы Заилийского Алатау.</p>',
        };
    }
}

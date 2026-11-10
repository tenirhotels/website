<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\SiteSettings;
use App\Filament\Resources\Events\EventResource;
use App\Filament\Resources\Hotels\HotelResource;
use App\Filament\Resources\Offers\OfferResource;
use App\Filament\Resources\RoomTypes\RoomTypeResource;
use App\Models\Event;
use App\Models\Experience;
use App\Models\Hotel;
use App\Models\Offer;
use App\Models\RoomType;
use App\Models\Setting;
use App\Models\Story;
use App\Models\Venue;
use App\Support\Locales;
use Filament\Widgets\Widget;

/** Чек-лист «что требует внимания»: пустые места, истёкшие предложения, непереведённое. */
class SiteHealth extends Widget
{
    protected static ?int $sort = 2;

    protected string $view = 'filament.widgets.site-health';

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $items = $this->checks();

        return ['items' => $items, 'problems' => collect($items)->where('level', 'warn')->count()];
    }

    /** @return list<array{level: string, text: string, url: ?string}> */
    private function checks(): array
    {
        $w = [];
        $add = function (string $level, string $text, ?string $url = null) use (&$w) {
            $w[] = ['level' => $level, 'text' => $text, 'url' => $url];
        };
        $today = now()->toDateString();

        foreach (Hotel::open()->published()->get() as $h) {
            if (! $h->hero_image) {
                $add('warn', "У отеля «{$h->t('name')}» нет главного фото", HotelResource::getUrl('edit', ['record' => $h]));
            }
        }
        foreach (RoomType::published()->where(fn ($q) => $q->whereNull('exely_room_type_id')->orWhere('exely_room_type_id', ''))->get() as $r) {
            $add('warn', "У номера «{$r->t('name')}» не указан ID в Exely — кнопка «Забронировать» откроет общую форму", RoomTypeResource::getUrl('edit', ['record' => $r]));
        }
        foreach (Offer::published()->whereNotNull('valid_to')->whereDate('valid_to', '<', $today)->get() as $o) {
            $add('warn', "Предложение «{$o->t('title')}» закончилось {$o->valid_to->format('d.m.Y')}, но ещё опубликовано", OfferResource::getUrl('edit', ['record' => $o]));
        }
        foreach (Offer::active()->whereNotNull('valid_to')->whereDate('valid_to', '<=', now()->addDays(14)->toDateString())->get() as $o) {
            $add('info', "Предложение «{$o->t('title')}» закончится {$o->valid_to->format('d.m.Y')}", OfferResource::getUrl('edit', ['record' => $o]));
        }
        if (Event::upcoming()->doesntExist()) {
            $add('info', 'В афише нет предстоящих событий — блок «События» скрыт на сайте', EventResource::getUrl('create'));
        }

        foreach (Hotel::open()->published()->get()->filter->hasCable() as $h) {
            $missingPhotos = collect(['medeu' => 'Медеу', 'shymbulak' => 'Шымбулак', 'combi1' => 'Средняя'])
                ->filter(fn ($name, $key) => ! $h->cableSetting("photo_$key"))->values();
            if ($missingPhotos->isNotEmpty()) {
                $add('warn', "«{$h->t('name')}»: нет фото станций для карточек поездки — ".$missingPhotos->implode(', ').' (карточка отеля → Канатная дорога)', HotelResource::getUrl('edit', ['record' => $h]));
            }
        }
        if (! Setting::get('contacts.phone') || ! Setting::get('contacts.email')) {
            $add('warn', 'Не заполнены телефон или e-mail отдела бронирования (Настройки → Контакты)', SiteSettings::getUrl());
        }

        // Переводы: опубликованные записи без текста на каждом языке.
        $sources = [[Hotel::class, 'name'], [RoomType::class, 'name'], [Venue::class, 'name'], [Experience::class, 'title'], [Offer::class, 'title'], [Event::class, 'title'], [Story::class, 'title']];
        foreach (Locales::codes() as $code) {
            $missing = 0;
            $total = 0;
            foreach ($sources as [$model, $field]) {
                foreach ($model::published()->get() as $row) {
                    $total++;
                    $v = $row->getAttribute($field);
                    if (! is_array($v) || trim((string) ($v[$code] ?? '')) === '') {
                        $missing++;
                    }
                }
            }
            if ($missing > 0) {
                $add('warn', 'Язык «'.(Locales::all()[$code]['name'] ?? $code)."»: без перевода названия — $missing из $total записей (на сайте подставляется другой язык)", null);
            }
        }

        return $w;
    }
}

<?php

namespace Tests\Feature;

use App\Models\RoomType;
use Tests\TestCase;

class RoomHighlightsTest extends TestCase
{
    public function test_default_list_pins_size_guests_bed_and_keeps_the_rest_as_list(): void
    {
        $r = new RoomType(['size_m2' => 32, 'max_guests' => 2]);
        $r->amenities = ['ru' => ['Финская сауна', 'Бесплатный Wi-Fi']];
        $r->setRelation('hotel', null);

        $this->assertSame(['32 м²', 'до 2 гостей'], array_column($r->tiles(), 'text'));
        $this->assertSame(['Финская сауна', 'Бесплатный Wi-Fi'], array_column($r->listFeatures(), 'text'));
        $this->assertSame(['sauna', 'wifi'], array_column($r->listFeatures(), 'icon'));
    }

    public function test_any_three_items_can_be_pinned_and_tiles_are_capped(): void
    {
        $r = new RoomType(['size_m2' => 32, 'max_guests' => 2]);
        $r->features = [
            ['source' => 'size', 'icon' => 'size', 'pin' => true],
            ['source' => 'custom', 'icon' => 'sauna', 'pin' => true, 'text' => ['ru' => 'Финская сауна']],
            ['source' => 'guests', 'icon' => 'guests', 'pin' => true],
            ['source' => 'custom', 'icon' => 'wifi', 'pin' => true, 'text' => ['ru' => 'Wi-Fi']],
            ['source' => 'custom', 'icon' => 'tv', 'pin' => false, 'text' => ['ru' => 'Телевизор']],
        ];
        $this->assertSame(['32 м²', 'Финская сауна', 'до 2 гостей'], array_column($r->tiles(), 'text'));
        $this->assertSame(['Телевизор'], array_column($r->listFeatures(), 'text'));
        $this->assertSame(['Финская сауна', 'Wi-Fi', 'Телевизор'], $r->amenityList());
    }

    public function test_empty_fact_is_skipped(): void
    {
        $r = new RoomType(['size_m2' => 32]);
        $r->features = [['source' => 'view', 'icon' => 'view', 'pin' => true], ['source' => 'size', 'icon' => 'size', 'pin' => true]];
        $this->assertSame(['32 м²'], array_column($r->tiles(), 'text'));
    }

    public function test_guests_label_splits_adults_and_children(): void
    {
        $r = new \App\Models\RoomType(['max_guests' => 3, 'max_children' => 1]);
        app()->setLocale('ru');
        $this->assertSame('до 3 гостей: 2 взрослых + 1 ребёнок', $r->guestsLabel());
        $this->assertSame('до 3 гостей', $r->guestsLabel(false));
        app()->setLocale('en');
        $this->assertSame('up to 3 guests: 2 adults + 1 child', $r->guestsLabel());
        $this->assertSame('up to 2 guests', (new \App\Models\RoomType(['max_guests' => 2]))->guestsLabel());
    }
}

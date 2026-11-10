<?php

namespace Tests\Feature;

use App\Models\RoomType;
use Tests\TestCase;

class RoomHighlightsTest extends TestCase
{
    public function test_highlights_pick_only_chosen_filled_facts(): void
    {
        $r = new RoomType(['size_m2' => 32, 'max_guests' => 2]);
        $r->highlights = ['size', 'guests', 'view'];
        $this->assertSame(['size', 'guests'], array_column($r->highlightFacts(), 0));

        $r->highlights = null;
        $this->assertSame(['size', 'guests'], array_column($r->highlightFacts(), 0));

        $r->highlights = ['guests'];
        $this->assertSame(['guests'], array_column($r->highlightFacts(), 0));
    }

    public function test_tiles_are_capped_at_three_and_pinned_features_leave_the_list(): void
    {
        $r = new RoomType(['size_m2' => 32, 'max_guests' => 2]);
        $r->highlights = ['size', 'guests'];
        $r->features = [
            ['icon' => 'sauna', 'pin' => true, 'text' => ['ru' => 'Финская сауна']],
            ['icon' => 'wifi', 'pin' => true, 'text' => ['ru' => 'Wi-Fi']],
            ['icon' => 'tv', 'pin' => false, 'text' => ['ru' => 'Телевизор']],
        ];
        $tiles = $r->tiles();
        $this->assertCount(3, $tiles);
        $this->assertSame('sauna', $tiles[2]['icon']);
        $this->assertSame(['Телевизор'], array_column($r->listFeatures(), 'text'));
        $this->assertSame(['Финская сауна', 'Wi-Fi', 'Телевизор'], $r->amenityList());
    }

    public function test_legacy_amenities_still_work_without_features(): void
    {
        $r = new RoomType;
        $r->amenities = ['ru' => ['Финская сауна', 'Бесплатный Wi-Fi']];
        $this->assertSame(['sauna', 'wifi'], array_column($r->featureList(), 'icon'));
    }
}

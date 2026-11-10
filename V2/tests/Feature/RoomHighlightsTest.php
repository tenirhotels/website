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
}

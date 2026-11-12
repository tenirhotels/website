<?php

namespace Tests\Feature;

use App\Filament\Resources\Events\EventResource;
use App\Filament\Resources\Events\Pages\CreateEvent;
use App\Filament\Resources\Events\Pages\ListEvents;
use App\Models\Event;
use App\Models\Hotel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EditorRoleTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    /** Сидер создаёт один отель — для проверок изоляции нужен второй. */
    private function hotels(): \Illuminate\Support\Collection
    {
        Hotel::firstOrCreate(['slug' => 'test-second'], ['name' => ['kk' => 'Second', 'ru' => 'Second', 'en' => 'Second']]);

        return Hotel::orderBy('id')->get();
    }

    private function makeEvent(?int $hotelId, string $title): Event
    {
        return Event::create([
            'hotel_id' => $hotelId,
            'slug' => str($title)->slug().'-2027-01',
            'title' => ['kk' => $title, 'ru' => $title, 'en' => $title],
            'starts_on' => '2027-01-10',
            'is_published' => true,
        ]);
    }

    private function editor(Hotel $hotel): User
    {
        return User::factory()->create(['role' => 'editor', 'hotel_id' => $hotel->id]);
    }

    public function test_editor_sees_only_own_hotel_content_and_no_admin_sections(): void
    {
        $hotels = $this->hotels();
        $this->assertGreaterThan(1, $hotels->count());
        [$mine, $other] = [$hotels[0], $hotels[1]];
        $own = $this->makeEvent($mine->id, 'Own event');
        $foreign = $this->makeEvent($other->id, 'Foreign event');
        $global = $this->makeEvent(null, 'Global event');

        $this->actingAs($this->editor($mine));
        $this->get('/admin')->assertOk()->assertSee('Вы редактируете');
        $this->get('/admin/events')->assertOk();

        Livewire::test(ListEvents::class)
            ->assertCanSeeTableRecords([$own])
            ->assertCanNotSeeTableRecords([$foreign, $global]);

        $this->get('/admin/events/'.$foreign->id.'/edit')->assertNotFound();
        foreach (['/admin/site-settings', '/admin/hotels', '/admin/redirects', '/admin/users', '/admin/pages'] as $url) {
            $this->get($url)->assertForbidden();
        }
    }

    public function test_editor_created_record_is_forced_to_own_hotel(): void
    {
        $hotels = $this->hotels();
        $this->actingAs($this->editor($hotels[0]));

        Livewire::test(CreateEvent::class)
            ->fillForm([
                'hotel_id' => $hotels[1]->id,
                'slug' => 'ski-day-2027-02',
                'starts_on' => '2027-02-01',
                'title.kk' => 'Шаңғы күні', 'title.ru' => 'Лыжный день', 'title.en' => 'Ski day',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertSame($hotels[0]->id, Event::where('slug', 'ski-day-2027-02')->value('hotel_id'));
    }

    public function test_model_hook_blocks_moving_record_to_another_hotel(): void
    {
        $hotels = $this->hotels();
        $event = $this->makeEvent($hotels[0]->id, 'Mine');
        $this->actingAs($this->editor($hotels[0]));
        $event->update(['hotel_id' => $hotels[1]->id]);
        $this->assertSame($hotels[0]->id, $event->fresh()->hotel_id);
    }

    public function test_admin_keeps_full_access_and_can_open_users(): void
    {
        $this->actingAs(User::factory()->create());
        foreach (['/admin', '/admin/users', '/admin/users/create', '/admin/site-settings', '/admin/hotels'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_editor_without_hotel_cannot_enter_panel(): void
    {
        $user = User::factory()->create(['role' => 'editor', 'hotel_id' => null]);
        $this->actingAs($user)->get('/admin')->assertForbidden();
    }
}

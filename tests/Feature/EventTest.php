<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    public function test_organizer_can_publish_and_users_can_register_once_and_cancel(): void
    {
        $organizer = User::factory()->create();
        $attendee = User::factory()->create();
        $this->actingAs($organizer)->post(route('events.store'), [
            'title' => 'Community Meetup', 'description' => 'Monthly event', 'venue' => 'Hall A',
            'starts_at' => now()->addDays(2)->format('Y-m-d H:i:s'), 'ends_at' => now()->addDays(2)->addHour()->format('Y-m-d H:i:s'), 'capacity' => 2,
        ])->assertRedirect();
        $event = Event::firstOrFail();
        $this->actingAs($attendee)->post(route('events.register', $event))->assertRedirect();
        $this->actingAs($attendee)->post(route('events.register', $event))->assertRedirect();
        $this->assertDatabaseCount('event_registrations', 1);
        $this->actingAs($organizer)->get(route('events.show', $event))->assertOk()->assertSee($attendee->name);
        $this->actingAs($attendee)->delete(route('events.cancel', $event))->assertRedirect();
        $this->assertDatabaseCount('event_registrations', 0);
    }

    public function test_capacity_and_organizer_authorization_are_enforced(): void
    {
        $organizer = User::factory()->create();
        $one = User::factory()->create();
        $two = User::factory()->create();
        $event = Event::create(['organizer_id' => $organizer->id, 'title' => 'Limited', 'description' => 'One spot', 'venue' => 'Room', 'starts_at' => now()->addDay(), 'ends_at' => now()->addDays(2), 'capacity' => 1, 'status' => 'published']);
        $this->actingAs($one)->post(route('events.register', $event))->assertRedirect();
        $this->actingAs($two)->post(route('events.register', $event))->assertSessionHasErrors('event');
        $this->actingAs($two)->patch(route('events.status', $event), ['status' => 'cancelled'])->assertNotFound();
        $this->actingAs($organizer)->patch(route('events.status', $event), ['status' => 'cancelled'])->assertRedirect();
        $this->actingAs($two)->get(route('events.show', $event))->assertNotFound();
    }

    public function test_event_dates_and_capacity_are_validated(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post(route('events.store'), ['title' => 'Invalid', 'description' => 'x', 'venue' => 'Room', 'starts_at' => now()->addDays(3), 'ends_at' => now()->addDays(2), 'capacity' => 0])->assertSessionHasErrors(['ends_at', 'capacity']);
    }
}

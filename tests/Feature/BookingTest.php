<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class BookingTest extends TestCase
{
    public function test_creates_a_booking_successfully(): void
    {
        $user = User::factory()->create();
        $room = Room::factory()->create();

        $this->actingAs($user)->postJson('/api/bookings', [
            'room_id'   => $room->id,
            'starts_at' => Carbon::tomorrow()->setTime(10, 0)->toIso8601String(),
            'ends_at'   => Carbon::tomorrow()->setTime(11, 0)->toIso8601String(),
            'title'     => 'Team Sync',
        ])
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'user_id', 'room', 'title', 'starts_at', 'ends_at'],
            ])
            ->assertJsonPath('data.user_id', $user->id)
            ->assertJsonPath('data.title', 'Team Sync');

        $this->assertSame(1, Booking::count());
    }

    public function test_returns_409_when_room_is_already_booked_for_that_time(): void
    {
        $user = User::factory()->create();
        $room = Room::factory()->create();

        Booking::factory()->create([
            'room_id'   => $room->id,
            'starts_at' => Carbon::tomorrow()->setTime(10, 0),
            'ends_at'   => Carbon::tomorrow()->setTime(11, 0),
        ]);

        $this->actingAs($user)->postJson('/api/bookings', [
            'room_id'   => $room->id,
            'starts_at' => Carbon::tomorrow()->setTime(10, 30)->toIso8601String(),
            'ends_at'   => Carbon::tomorrow()->setTime(11, 30)->toIso8601String(),
        ])
            ->assertStatus(409)
            ->assertJsonPath('error', 'conflict');
    }

    public function test_allows_booking_same_room_in_non_overlapping_slot(): void
    {
        $user = User::factory()->create();
        $room = Room::factory()->create();

        Booking::factory()->create([
            'room_id'   => $room->id,
            'starts_at' => Carbon::tomorrow()->setTime(9, 0),
            'ends_at'   => Carbon::tomorrow()->setTime(10, 0),
        ]);

        $this->actingAs($user)->postJson('/api/bookings', [
            'room_id'   => $room->id,
            'starts_at' => Carbon::tomorrow()->setTime(10, 0)->toIso8601String(),
            'ends_at'   => Carbon::tomorrow()->setTime(11, 0)->toIso8601String(),
        ])
            ->assertStatus(201);
    }

    public function test_validates_required_fields(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson('/api/bookings', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['room_id', 'starts_at', 'ends_at']);
    }

    public function test_validates_room_exists(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson('/api/bookings', [
            'room_id'   => 9999,
            'starts_at' => Carbon::tomorrow()->setTime(10, 0)->toIso8601String(),
            'ends_at'   => Carbon::tomorrow()->setTime(11, 0)->toIso8601String(),
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['room_id']);
    }

    public function test_validates_ends_at_is_after_starts_at(): void
    {
        $user = User::factory()->create();
        $room = Room::factory()->create();

        $this->actingAs($user)->postJson('/api/bookings', [
            'room_id'   => $room->id,
            'starts_at' => Carbon::tomorrow()->setTime(11, 0)->toIso8601String(),
            'ends_at'   => Carbon::tomorrow()->setTime(10, 0)->toIso8601String(),
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['ends_at']);
    }

    public function test_store_returns_401_for_unauthenticated_requests(): void
    {
        $this->postJson('/api/bookings', [])->assertStatus(401);
    }

    // GET /api/bookings/my

    public function test_returns_only_authenticated_users_bookings(): void
    {
        $user  = User::factory()->create();
        $other = User::factory()->create();
        $room  = Room::factory()->create();

        Booking::factory()->count(3)->create(['user_id' => $user->id,  'room_id' => $room->id]);
        Booking::factory()->count(2)->create(['user_id' => $other->id, 'room_id' => $room->id]);

        $this->actingAs($user)->getJson('/api/bookings/my')
            ->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_returns_empty_array_when_user_has_no_bookings(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->getJson('/api/bookings/my')
            ->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    public function test_my_returns_401_for_unauthenticated_requests(): void
    {
        $this->getJson('/api/bookings/my')->assertStatus(401);
    }

    // GET /api/bookings/by-room

    public function test_returns_bookings_for_a_specific_room(): void
    {
        $user  = User::factory()->create();
        $room1 = Room::factory()->create();
        $room2 = Room::factory()->create();

        Booking::factory()->count(4)->create(['room_id' => $room1->id]);
        Booking::factory()->count(1)->create(['room_id' => $room2->id]);

        $this->actingAs($user)->getJson("/api/bookings/by-room?room_id={$room1->id}")
            ->assertStatus(200)
            ->assertJsonCount(4, 'data');
    }

    public function test_returns_empty_array_when_room_has_no_bookings(): void
    {
        $user = User::factory()->create();
        $room = Room::factory()->create();

        $this->actingAs($user)->getJson("/api/bookings/by-room?room_id={$room->id}")
            ->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }

    public function test_validates_room_id_exists(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->getJson('/api/bookings/by-room?room_id=9999')
            ->assertStatus(422)
            ->assertJsonValidationErrors(['room_id']);
    }

    public function test_by_room_returns_401_for_unauthenticated_requests(): void
    {
        $this->getJson('/api/bookings/by-room?room_id=1')->assertStatus(401);
    }
}

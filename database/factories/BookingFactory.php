<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $startsAt = $this->faker->dateTimeBetween('+1 day', '+30 days');
        $endsAt   = (clone $startsAt)->modify('+1 hour');

        return [
            'user_id'   => $this->faker->numberBetween(1, 100),
            'room_id'   => Room::factory(),
            'starts_at' => $startsAt,
            'ends_at'   => $endsAt,
            'title'     => $this->faker->optional()->sentence(3),
        ];
    }
}

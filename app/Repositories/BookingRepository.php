<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTOs\CreateBookingDTO;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Collection;

class BookingRepository
{
    public function hasConflict(CreateBookingDTO $dto): bool
    {
        return Booking::where('room_id', $dto->roomId)
            ->where('starts_at', '<', $dto->endsAt)
            ->where('ends_at', '>', $dto->startsAt)
            ->lockForUpdate()
            ->exists();
    }

    public function create(CreateBookingDTO $dto): Booking
    {
        return Booking::create([
            'user_id'   => $dto->userId,
            'room_id'   => $dto->roomId,
            'starts_at' => $dto->startsAt,
            'ends_at'   => $dto->endsAt,
            'title'     => $dto->title,
        ]);
    }

    public function getByUser(int $userId): Collection
    {
        return Booking::with('room')
            ->where('user_id', $userId)
            ->orderBy('starts_at')
            ->get();
    }

    public function getByRoom(int $roomId): Collection
    {
        return Booking::with('room')
            ->where('room_id', $roomId)
            ->orderBy('starts_at')
            ->get();
    }
}

<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\CreateBookingDTO;
use App\Exceptions\RoomConflictException;
use App\Models\Booking;
use App\Repositories\BookingRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

readonly class BookingService
{
    public function __construct(
        private BookingRepository $repository,
    ) {}

    /**
     * @throws RoomConflictException|Throwable
     */
    public function create(CreateBookingDTO $dto): Booking
    {
        return DB::transaction(function () use ($dto): Booking {
            if ($this->repository->hasConflict($dto)) {
                throw new RoomConflictException();
            }

            return $this->repository->create($dto);
        });
    }

    public function getByUser(int $userId): Collection
    {
        return $this->repository->getByUser($userId);
    }

    public function getByRoom(int $roomId): Collection
    {
        return $this->repository->getByRoom($roomId);
    }
}

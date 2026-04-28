<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Http\Requests\CreateBookingRequest;
use Carbon\Carbon;

class CreateBookingDTO
{
    public function __construct(
        public int    $userId,
        public int    $roomId,
        public Carbon $startsAt,
        public Carbon $endsAt,
        public ?string $title,
    ) {}

    public static function fromRequest(CreateBookingRequest $request): self
    {
        return new self(
            userId:   $request->integer('user_id'),
            roomId:   $request->integer('room_id'),
            startsAt: Carbon::parse($request->string('starts_at')->value()),
            endsAt:   Carbon::parse($request->string('ends_at')->value()),
            title:    $request->string('title')->value() ?: null,
        );
    }
}

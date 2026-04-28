<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTOs\CreateBookingDTO;
use App\Exceptions\RoomConflictException;
use App\Http\Requests\CreateBookingRequest;
use App\Http\Resources\BookingResource;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BookingController extends Controller
{
    public function __construct(
        private readonly BookingService $bookingService,
    ) {}

    public function store(CreateBookingRequest $request): JsonResponse
    {
        try {
            $booking = $this->bookingService->create(
                CreateBookingDTO::fromRequest($request)
            );

            return response()->json(
                new BookingResource($booking->load('room')),
                201
            );
        } catch (RoomConflictException $e) {
            return response()->json([
                'error'   => 'conflict',
                'message' => $e->getMessage(),
            ], 409);
        }
    }

    public function byUser(Request $request): AnonymousResourceCollection
    {
        $bookings = $this->bookingService->getByUser(
            $request->user()->id
        );

        return BookingResource::collection($bookings);
    }

    public function byRoom(Request $request): AnonymousResourceCollection
    {
        $request->validate(['room_id' => ['required', 'integer', 'exists:rooms,id']]);

        $bookings = $this->bookingService->getByRoom(
            $request->integer('room_id')
        );

        return BookingResource::collection($bookings);
    }
}

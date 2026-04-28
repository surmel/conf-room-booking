<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'user_id'    => $this->user_id,
            'room'       => $this->whenLoaded('room', fn () => [
                'id'       => $this->room->id,
                'name'     => $this->room->name,
                'capacity' => $this->room->capacity,
            ]),
            'title'      => $this->title,
            'starts_at'  => $this->starts_at->toIso8601String(),
            'ends_at'    => $this->ends_at->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}

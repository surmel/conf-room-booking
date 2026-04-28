<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id'   => ['required', 'integer', 'exists:users,id'],
            'room_id'   => ['required', 'integer', 'exists:rooms,id'],
            'starts_at' => ['required', 'date', 'after:now'],
            'ends_at'   => ['required', 'date', 'after:starts_at'],
            'title'     => ['nullable', 'string', 'max:255'],
        ];
    }
}

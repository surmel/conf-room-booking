<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class RoomConflictException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('The room is already booked for the requested time slot.');
    }
}

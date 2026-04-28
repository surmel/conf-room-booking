<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = [
            ['name' => 'One',   'capacity' => 6],
            ['name' => 'Two',    'capacity' => 10],
            ['name' => 'Three',   'capacity' => 4],
            ['name' => 'Four',   'capacity' => 20],
            ['name' => 'Five', 'capacity' => 8],
        ];

        foreach ($rooms as $room) {
            Room::firstOrCreate(['name' => $room['name']], $room);
        }
    }
}

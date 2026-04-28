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
            ['name' => 'Room One',   'capacity' => 6],
            ['name' => 'Room Two',   'capacity' => 10],
            ['name' => 'Room Three', 'capacity' => 4],
            ['name' => 'Room Four',  'capacity' => 20],
            ['name' => 'Room Five',  'capacity' => 8],
        ];

        foreach ($rooms as $room) {
            Room::firstOrCreate(['name' => $room['name']], $room);
        }
    }
}

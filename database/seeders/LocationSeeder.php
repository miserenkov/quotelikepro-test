<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        array_map(Location::create(...), [
            ['name' => 'Киев', 'price_modifier' => 2],
            ['name' => 'Львов', 'price_modifier' => -3],
            ['name' => 'Одесса', 'price_modifier' => 0],
        ]);
    }
}

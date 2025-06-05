<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        array_map(Category::create(...), [
            ['name' => 'Электроника', 'price_modifier' => 5],
            ['name' => 'Мебель', 'price_modifier' => -10],
            ['name' => 'Одежда', 'price_modifier' => 0],
        ]);
    }
}

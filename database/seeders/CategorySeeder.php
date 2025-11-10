<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Catalog\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['name' => 'Batteries', 'slug' => 'batteries'],
            ['name' => 'Solar Panels', 'slug' => 'solar-panels'],
            ['name' => 'Connectors', 'slug' => 'connectors'],
        ];
        foreach ($data as $row) {
            Category::firstOrCreate(['slug' => $row['slug']], $row);
        }
    }
}

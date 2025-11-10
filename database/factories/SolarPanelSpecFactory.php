<?php

namespace Database\Factories;

use App\Domain\Catalog\Models\SolarPanelSpec;
use App\Domain\Catalog\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class SolarPanelSpecFactory extends Factory
{
    protected $model = SolarPanelSpec::class;

    public function definition(): array
    {
        return ['product_id' => Product::factory(), 'power_output' => $this->faker->numberBetween(300, 600)];
    }
}

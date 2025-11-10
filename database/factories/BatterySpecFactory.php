<?php

namespace Database\Factories;

use App\Domain\Catalog\Models\BatterySpec;
use App\Domain\Catalog\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class BatterySpecFactory extends Factory
{
    protected $model = BatterySpec::class;

    public function definition(): array
    {
        return ['product_id' => Product::factory(), 'capacity' => $this->faker->randomFloat(1, 2.0, 15.0)];
    }
}

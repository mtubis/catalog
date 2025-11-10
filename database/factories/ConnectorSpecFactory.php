<?php

namespace Database\Factories;

use App\Domain\Catalog\Models\ConnectorSpec;
use App\Domain\Catalog\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConnectorSpecFactory extends Factory
{
    protected $model = ConnectorSpec::class;

    public function definition(): array
    {
        return ['product_id' => Product::factory(), 'connector_type' => $this->faker->randomElement(['MC4','Type2','XT60','Anderson'])];
    }
}

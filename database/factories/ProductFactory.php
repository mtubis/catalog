<?php

namespace Database\Factories;

use App\Domain\Catalog\Models\Product;
use App\Domain\Catalog\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'category_id'     => Category::factory(),           // relationship without side-effects create()
            'name'            => $this->faker->words(3, true),
            'manufacturer'    => $this->faker->randomElement(['SunCorp','VoltX','MegaWatt','GreenCo','A','B','C']),
            'price'           => $this->faker->randomFloat(2, 10, 2000),
            'description'     => $this->faker->sentence(),
            'source_category' => 'factory',                     // will be overwritten by forCategory()
            'source_id'       => (string) Str::uuid(),
        ];
    }

    public function forCategory(Category $c): self
    {
        return $this->state(fn() => [
            'category_id' => $c->id,
            'source_category' => $c->slug,
        ]);
    }
}

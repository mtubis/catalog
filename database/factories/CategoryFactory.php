<?php

namespace Database\Factories;

use App\Domain\Catalog\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = $this->faker->words(2, true); // np. "alpha gamma"
        return [
            'name' => ucfirst($name),
            // random, practically unique slug - does not interfere with indexes
            'slug' => Str::slug($name.'-'.Str::random(6)),
        ];
    }

    public function batteries(): self
    {
        return $this->state(fn () => ['name' => 'Batteries', 'slug' => 'batteries']);
    }

    public function solar(): self
    {
        return $this->state(fn () => ['name' => 'Solar Panels', 'slug' => 'solar-panels']);
    }

    public function connectors(): self
    {
        return $this->state(fn () => ['name' => 'Connectors', 'slug' => 'connectors']);
    }
}

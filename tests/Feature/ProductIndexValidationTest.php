<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductIndexValidationTest extends TestCase
{
    use RefreshDatabase;

    use HasFactory;

    public function test_validates_price_ranges(): void
    {
        $this->getJson('/api/products?price_min=100&price_max=50')
            ->assertStatus(422)
            ->assertJsonValidationErrors(['price_max']);
    }

    public function test_validates_connectors_input_shape(): void
    {
        $this->getJson('/api/products?connector_type=MC4')
            ->assertStatus(422)
            ->assertJsonValidationErrors(['connector_type']);
    }
}

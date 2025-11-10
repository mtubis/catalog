<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domain\Catalog\Models\{Category, Product};

class ProductPaginationTest extends TestCase
{
    use RefreshDatabase;

    use HasFactory;

    public function test_paginates_and_respects_per_page(): void
    {
        $cat = Category::factory()->batteries()->create();
        Product::factory()->count(5)->forCategory($cat)->create();

        $res = $this->getJson('/api/products?per_page=2')->assertOk()->json();
        $this->assertCount(2, $res['data']);
        $this->assertArrayHasKey('links', $res);
        $this->assertArrayHasKey('meta', $res);
    }
}

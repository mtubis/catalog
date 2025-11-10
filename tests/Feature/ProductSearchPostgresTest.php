<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use App\Domain\Catalog\Models\{Category, Product};

class ProductSearchPostgresTest extends TestCase
{
    use RefreshDatabase;

    use HasFactory;

    protected function setUp(): void
    {
        parent::setUp();
        if (DB::connection()->getDriverName() !== 'pgsql') {
            $this->markTestSkipped('FTS test skipped: not using PostgreSQL');
        }
    }

    public function test_searches_across_fields(): void
    {
        $sol = Category::factory()->solar()->create();
        $p = Product::factory()->forCategory($sol)->create([
            'name' => 'Alpha Mono 400W',
            'manufacturer' => 'SunCorp',
            'description' => 'High efficiency PERC cells.',
        ]);

        $this->getJson('/api/products?q=SunCorp PERC')
            ->assertOk()
            ->assertJsonFragment(['id' => $p->id]);
    }
}

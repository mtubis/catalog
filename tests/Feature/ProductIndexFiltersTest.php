<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domain\Catalog\Models\{Category, Product, BatterySpec, SolarPanelSpec, ConnectorSpec};

class ProductIndexFiltersTest extends TestCase
{
    use RefreshDatabase;

    use HasFactory;

    public function test_filters_by_category_and_price_range(): void
    {
        $batt = Category::factory()->batteries()->create();
        $cheap = Product::factory()->forCategory($batt)->create(['price' => 100]);
        $mid   = Product::factory()->forCategory($batt)->create(['price' => 350]);
        $exp   = Product::factory()->forCategory($batt)->create(['price' => 900]);

        $res = $this->getJson('/api/products?category=batteries&price_min=200&price_max=500')
            ->assertOk()->json('data');

        $ids = collect($res)->pluck('id');
        $this->assertTrue($ids->contains($mid->id));
        $this->assertFalse($ids->contains($cheap->id));
        $this->assertFalse($ids->contains($exp->id));
    }

    public function test_filters_batteries_by_capacity_range(): void
    {
        $batt = Category::factory()->batteries()->create();
        $p = Product::factory()->forCategory($batt)->create();
        BatterySpec::factory()->create(['product_id' => $p->id, 'capacity' => 7.5]);

        $this->getJson('/api/products?category=batteries&capacity_min=7&capacity_max=8')
            ->assertOk()
            ->assertJsonFragment(['id' => $p->id]);
    }

    public function test_filters_solar_by_power_output(): void
    {
        $sol = Category::factory()->solar()->create();
        $p = Product::factory()->forCategory($sol)->create();
        SolarPanelSpec::factory()->create(['product_id' => $p->id, 'power_output' => 420]);

        $this->getJson('/api/products?category=solar-panels&power_min=400&power_max=500')
            ->assertOk()
            ->assertJsonFragment(['id' => $p->id]);
    }

    public function test_filters_connectors_by_types(): void
    {
        $conn = Category::factory()->connectors()->create();
        $mc4 = Product::factory()->forCategory($conn)->create();
        $t2  = Product::factory()->forCategory($conn)->create();
        ConnectorSpec::factory()->create(['product_id' => $mc4->id, 'connector_type' => 'MC4']);
        ConnectorSpec::factory()->create(['product_id' => $t2->id,  'connector_type' => 'Type2']);

        $res = $this->getJson('/api/products?category=connectors&connector_type[]=MC4&connector_type[]=Type2')
            ->assertOk()->json('data');

        $ids = collect($res)->pluck('id');
        $this->assertTrue($ids->contains($mc4->id));
        $this->assertTrue($ids->contains($t2->id));
    }

    public function test_filters_by_multiple_manufacturers(): void
    {
        $cat = Category::factory()->batteries()->create();
        $a = Product::factory()->forCategory($cat)->create(['manufacturer' => 'A']);
        $b = Product::factory()->forCategory($cat)->create(['manufacturer' => 'B']);
        $c = Product::factory()->forCategory($cat)->create(['manufacturer' => 'C']);

        $res = $this->getJson('/api/products?manufacturer[]=A&manufacturer[]=C')
            ->assertOk()->json('data');

        $mans = collect($res)->pluck('manufacturer');
        $this->assertTrue($mans->contains('A'));
        $this->assertTrue($mans->contains('C'));
        $this->assertFalse($mans->contains('B'));
    }
}

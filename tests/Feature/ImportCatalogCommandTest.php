<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domain\Catalog\Models\{Product, BatterySpec, SolarPanelSpec, ConnectorSpec};

class ImportCatalogCommandTest extends TestCase
{
    use RefreshDatabase;

    use HasFactory;

    private function writeCsv(string $path, array $rows): void
    {
        $dir = dirname($path);
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        $fp = fopen($path, 'w');
        fputcsv($fp, array_keys($rows[0]));
        foreach ($rows as $r) fputcsv($fp, array_values($r));
        fclose($fp);
    }

    public function test_imports_csvs_and_is_idempotent(): void
    {
        $dir = base_path('storage/app/testing');
        $this->writeCsv("$dir/batteries.csv", [
            ['id'=>1,'name'=>'Bat A','manufacturer'=>'A','price'=>100,'capacity'=>5.0,'description'=>'x'],
        ]);
        $this->writeCsv("$dir/solar_panels.csv", [
            ['id'=>1,'name'=>'Panel A','manufacturer'=>'S','price'=>150,'power_output'=>400,'description'=>'y'],
        ]);
        $this->writeCsv("$dir/connectors.csv", [
            ['id'=>1,'name'=>'Conn A','manufacturer'=>'C','price'=>10,'connector_type'=>'MC4','description'=>'z'],
        ]);

        $this->artisan('catalog:import', ['--dir' => 'storage/app/testing'])->assertExitCode(0);
        $this->assertSame(3, Product::count());
        $this->assertSame(1, BatterySpec::count());
        $this->assertSame(1, SolarPanelSpec::count());
        $this->assertSame(1, ConnectorSpec::count());

        // run again -> no duplicates
        $this->artisan('catalog:import', ['--dir' => 'storage/app/testing'])->assertExitCode(0);
        $this->assertSame(3, Product::count());
    }
}

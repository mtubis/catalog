<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Domain\Catalog\Models\{Category, Product, BatterySpec, SolarPanelSpec, ConnectorSpec};

class ImportCatalog extends Command
{
    protected $signature = 'catalog:import {--dir=storage/app/data}';
    protected $description = 'Import CSV catalog files into the database';


    public function handle(): int
    {
        $dir = base_path($this->option('dir'));
        $files = [
            'batteries' => $dir.'/batteries.csv',
            'solar-panels' => $dir.'/solar_panels.csv',
            'connectors' => $dir.'/connectors.csv',
        ];


        foreach ($files as $slug => $path) {
            if (!is_file($path)) { $this->warn("Missing file: $path"); continue; }
            $this->importFile($slug, $path);
        }


        $this->info('Import done.');
        return self::SUCCESS;
    }

    protected function importFile(string $categorySlug, string $path): void
    {
        $this->line("Importing $categorySlug from $path ...");
        $category = Category::firstOrCreate(['slug' => $categorySlug], ['name' => ucwords(str_replace('-', ' ', $categorySlug))]);


        $rows = $this->readCsv($path);
        DB::transaction(function () use ($rows, $category, $categorySlug) {
            foreach ($rows as $i => $row) {
                // Basic validation
                foreach (['id','name','manufacturer','price','description'] as $col) {
                    if (!array_key_exists($col, $row)) {
                        throw new \RuntimeException("Missing column '$col' in row $i");
                    }
                }


                $product = Product::updateOrCreate(
                    ['source_category' => $categorySlug, 'source_id' => (string)$row['id']],
                    [
                        'category_id' => $category->id,
                        'name' => $row['name'],
                        'manufacturer' => $row['manufacturer'],
                        'price' => (float) $row['price'],
                        'description' => $row['description'] ?? null,
                    ]
                );


                match ($categorySlug) {
                    'batteries' => BatterySpec::updateOrCreate(
                        ['product_id' => $product->id],
                        ['capacity' => (float) $row['capacity']]
                    ),
                    'solar-panels' => SolarPanelSpec::updateOrCreate(
                        ['product_id' => $product->id],
                        ['power_output' => (int) $row['power_output']]
                    ),
                    'connectors' => ConnectorSpec::updateOrCreate(
                        ['product_id' => $product->id],
                        ['connector_type' => (string) $row['connector_type']]
                    ),
                    default => null,
                };
            }
        });
    }

    protected function readCsv(string $path): array
    {
        $rows = [];
        if (($h = fopen($path, 'r')) === false) throw new \RuntimeException("Cannot open $path");
        $headers = fgetcsv($h);
        while (($data = fgetcsv($h)) !== false) {
            $rows[] = array_combine($headers, $data);
        }
        fclose($h);
        return $rows;
    }
}

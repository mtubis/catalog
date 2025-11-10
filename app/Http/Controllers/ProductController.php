<?php

namespace App\Http\Controllers;

use App\Domain\Catalog\Models\Product;
use App\Http\Requests\ProductIndexRequest;
use Illuminate\Pipeline\Pipeline;
use App\Domain\Catalog\Filters\{SearchFilter,CategoryFilter,ManufacturerFilter,PriceRangeFilter,BatteryCapacityFilter,SolarPowerFilter,ConnectorTypeFilter};
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    public function index(ProductIndexRequest $request)
    {
        $data = $request->validated();
        $query = Product::query()->with(['category','batterySpec','solarPanelSpec','connectorSpec']);


        $filters = [
            new SearchFilter($data['q'] ?? null),
            new CategoryFilter($data['category'] ?? null),
            new ManufacturerFilter($data['manufacturer'] ?? null),
            new PriceRangeFilter($data['price_min'] ?? null, $data['price_max'] ?? null),
            new BatteryCapacityFilter($data['capacity_min'] ?? null, $data['capacity_max'] ?? null),
            new SolarPowerFilter($data['power_min'] ?? null, $data['power_max'] ?? null),
            new ConnectorTypeFilter($data['connector_type'] ?? null),
        ];


        $query = app(Pipeline::class)->send($query)->through($filters)->thenReturn();


        $perPage = $data['per_page'] ?? 15;
        return ProductResource::collection($query->paginate($perPage));
    }
}

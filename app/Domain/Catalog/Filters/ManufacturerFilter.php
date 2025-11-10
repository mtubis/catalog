<?php

namespace App\Domain\Catalog\Filters;

use Illuminate\Database\Eloquent\Builder;

class ManufacturerFilter extends BaseFilter
{
    public function __construct(private readonly ?array $manufacturers) {}


    protected function apply(Builder $query): Builder
    {
        if (!$this->manufacturers) return $query;
        return $query->whereIn('manufacturer', $this->manufacturers);
    }
}

<?php

namespace App\Domain\Catalog\Filters;

use Illuminate\Database\Eloquent\Builder;

class PriceRangeFilter extends BaseFilter
{
    public function __construct(private readonly ?float $min, private readonly ?float $max) {}


    protected function apply(Builder $query): Builder
    {
        return $query
            ->when($this->min !== null, fn($q) => $q->where('price', '>=', $this->min))
            ->when($this->max !== null, fn($q) => $q->where('price', '<=', $this->max));
    }
}

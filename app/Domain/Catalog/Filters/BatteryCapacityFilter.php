<?php

namespace App\Domain\Catalog\Filters;

use Illuminate\Database\Eloquent\Builder;

class BatteryCapacityFilter extends BaseFilter
{
    public function __construct(private readonly ?float $min, private readonly ?float $max) {}


    protected function apply(Builder $query): Builder
    {
        if ($this->min === null && $this->max === null) return $query;
        return $query->whereHas('batterySpec', function ($q) {
            $q->when($this->min !== null, fn($qq) => $qq->where('capacity', '>=', $this->min))
                ->when($this->max !== null, fn($qq) => $qq->where('capacity', '<=', $this->max));
        });
    }
}

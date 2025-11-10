<?php

namespace App\Domain\Catalog\Filters;

use Illuminate\Database\Eloquent\Builder;

class SolarPowerFilter extends BaseFilter
{
    public function __construct(private readonly ?int $min, private readonly ?int $max) {}


    protected function apply(Builder $query): Builder
    {
        if ($this->min === null && $this->max === null) return $query;
        return $query->whereHas('solarPanelSpec', function ($q) {
            $q->when($this->min !== null, fn($qq) => $qq->where('power_output', '>=', $this->min))
                ->when($this->max !== null, fn($qq) => $qq->where('power_output', '<=', $this->max));
        });
    }
}

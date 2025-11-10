<?php

namespace App\Domain\Catalog\Filters;

use Illuminate\Database\Eloquent\Builder;

class ConnectorTypeFilter extends BaseFilter
{
    public function __construct(private readonly ?array $types) {}


    protected function apply(Builder $query): Builder
    {
        if (!$this->types) return $query;
        return $query->whereHas('connectorSpec', fn($q) => $q->whereIn('connector_type', $this->types));
    }
}

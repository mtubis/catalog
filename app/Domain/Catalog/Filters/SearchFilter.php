<?php

namespace App\Domain\Catalog\Filters;

use Illuminate\Database\Eloquent\Builder;

class SearchFilter extends BaseFilter
{
    public function __construct(private readonly ?string $q) {}


    protected function apply(Builder $query): Builder
    {
        if (!$this->q) return $query;
        // PostgreSQL: web style search
        return $query->whereRaw("searchable @@ websearch_to_tsquery('simple', ?)", [$this->q]);
    }
}

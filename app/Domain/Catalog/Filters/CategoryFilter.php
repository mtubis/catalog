<?php

namespace App\Domain\Catalog\Filters;

use Illuminate\Database\Eloquent\Builder;

class CategoryFilter extends BaseFilter
{
    public function __construct(private readonly ?string $slug) {}


    protected function apply(Builder $query): Builder
    {
        if (!$this->slug) return $query;
        return $query->whereHas('category', fn($q) => $q->where('slug', $this->slug));
    }
}

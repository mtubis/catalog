<?php

namespace App\Domain\Catalog\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;

abstract class BaseFilter
{
    public function handle(Builder $query, Closure $next): Builder
    {
        $query = $this->apply($query);
        return $next($query);
    }


    abstract protected function apply(Builder $query): Builder;
}

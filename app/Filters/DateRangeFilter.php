<?php

namespace App\Filters;

use Illuminate\Support\Carbon;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class DateRangeFilter implements Filter
{
    public function __invoke(Builder $query, $dates, string $column): Builder
    {
        $start = Carbon::createFromFormat('Y-m-d', $dates['start'])->startOfDay();
        $end = Carbon::createFromFormat('Y-m-d', $dates['end'])->endOfDay();

        return $query->whereBetween($column, [$start, $end]);
    }
}

<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Str;

class CountryScope implements Scope
{
    /**
     * @param Builder $builder
     * @param Model $model
     */
    public function apply(Builder $builder, Model $model): void
    {
        $host = request()->getHost();
        $domainPattern = '.' . config('app.domain');
        $country = Str::contains($host, $domainPattern)
            ? Str::before($host, $domainPattern)
            : null;

        if ($country) {
            $builder->where('country', $country);
        }
    }
}

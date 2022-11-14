<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class LocationsService
{
    /**
     * @param string|null $country
     * @return string[]
     */
    public function asSelectArray(?string $country): array
    {
        $locations = $this->getLocationCollection($country);

        if ($locations) {
            return $locations->toArray();
        }

        return [];
    }

    /**
     * @param string|null $country
     * @return string[]
     */
    public function getValues(?string $country): array
    {
        $locations = $this->getLocationCollection($country);

        if ($locations) {
            return $locations->keys()->toArray();
        }

        return [];
    }

    public function getDescription(?string $country, ?string $key): string
    {
        $locations = $this->getLocationCollection($country);

        if ($key && $locations) {
            return $locations->has($key)
                ? $locations->get($key)
                : $key;
        }

        return (string)$key;
    }

    /**
     * @param string|null $country
     * @return Collection|null
     */
    private function getLocationCollection(?string $country): ?Collection
    {
        if ($country) {
            $collection = collect(config('countries.' . Str::lower($country) . '.locations'));

            if ($collection->isNotEmpty()) {
                $locationLocale = array_key_exists(app()->getLocale(), $collection->first())
                    ? app()->getLocale()
                    : config('app.fallback_locale');

                return $collection->map(function ($_item) use ($locationLocale) {
                    return $_item[$locationLocale];
                });
            }
        }

        return null;
    }
}

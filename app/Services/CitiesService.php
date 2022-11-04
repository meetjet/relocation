<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CitiesService
{
    /**
     * @param string|null $country
     * @return string[]
     */
    public function asSelectArray(?string $country): array
    {
        $cities = $this->getCityCollection($country);

        if ($cities) {
            return $cities->toArray();
        }

        return [];
    }

    /**
     * @param string|null $country
     * @return string[]
     */
    public function getValues(?string $country): array
    {
        $cities = $this->getCityCollection($country);

        if ($cities) {
            return $cities->keys()->toArray();
        }

        return [];
    }

    public function getDescription(?string $country, ?string $key): string
    {
        $cities = $this->getCityCollection($country);

        if ($key && $cities) {
            return $cities->has($key)
                ? $cities->get($key)
                : $key;
        }

        return (string)$key;
    }

    /**
     * @param string|null $country
     * @return Collection|null
     */
    private function getCityCollection(?string $country): ?Collection
    {
        if ($country) {
            $collection = collect(config('cities.' . Str::lower($country)));

            if ($collection->isNotEmpty()) {
                $cityLocale = array_key_exists(app()->getLocale(), $collection->first())
                    ? app()->getLocale()
                    : config('app.fallback_locale');

                return $collection->map(function ($_item) use ($cityLocale) {
                    return $_item[$cityLocale];
                });
            }
        }

        return null;
    }
}

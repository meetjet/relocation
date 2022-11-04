<?php

namespace App\Services\Cities;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CitiesService
{
    private Collection $cityCollectionCache;

    public function __construct()
    {
        $this->cityCollectionCache = collect();
    }

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
            if ($this->cityCollectionCache->has($country)) {
                return $this->cityCollectionCache->get($country);
            }

            $filepath = app_path('Services/Cities/' . Str::lower($country) . '.json');

            if (File::exists($filepath)) {
                $collection = collect(json_decode(File::get($filepath), true));

                $cityLocale = array_key_exists(app()->getLocale(), $collection->first())
                    ? app()->getLocale()
                    : config('app.fallback_locale');

                $cityCollection = $collection
                    ->sortBy($cityLocale)
                    ->map(function ($_item) use ($cityLocale) {
                        return $_item[$cityLocale];
                    });

                $this->cityCollectionCache->put($country, $cityCollection);

                return $cityCollection;
            }
        }

        return null;
    }
}

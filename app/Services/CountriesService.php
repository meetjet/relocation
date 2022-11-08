<?php

namespace App\Services;

use Illuminate\Support\Collection;

class CountriesService
{
    /**
     * @return string[]
     */
    public function asSelectArray(): array
    {
        $countries = $this->getCountryLocaleCollection();

        if ($countries) {
            return $countries->toArray();
        }

        return [];
    }

    /**
     * @return string[]
     */
    public function getValues(): array
    {
        $countries = $this->getCountryLocaleCollection();

        if ($countries) {
            return $countries->keys()->toArray();
        }

        return [];
    }

    public function getDescription(?string $key): string
    {
        $countries = $this->getCountryLocaleCollection();

        if ($key && $countries) {
            return $countries->has($key)
                ? $countries->get($key)
                : $key;
        }

        return (string)$key;
    }

    /**
     * @return Collection|null
     */
    private function getCountryLocaleCollection(): ?Collection
    {
            $collection = collect(config('countries'));

            if ($collection->isNotEmpty()) {
                $countryLocale = array_key_exists(app()->getLocale(), $collection->first()['locale'])
                    ? app()->getLocale()
                    : config('app.fallback_locale');

                return $collection->map(function ($_item) use ($countryLocale) {
                    return $_item['locale'][$countryLocale];
                });
            }

        return null;
    }
}

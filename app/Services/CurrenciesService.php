<?php

namespace App\Services;

use Illuminate\Support\Collection;

class CurrenciesService
{
    /**
     * @return string[]
     */
    public function asSelectArray(): array
    {
        $currencies = $this->getCurrencyCollection();

        if ($currencies) {
            return $currencies->map(function ($_item) {
                return $_item['title'];
            })->toArray();
        }

        return [];
    }

    /**
     * @return string[]
     */
    public function getValues(): array
    {
        $currencies = $this->getCurrencyCollection();

        if ($currencies) {
            return $currencies->keys()->toArray();
        }

        return [];
    }

    /**
     * @param string|null $key
     * @return string
     */
    public function getDescription(?string $key): string
    {
        $currencies = $this->getCurrencyCollection();

        if ($key && $currencies) {
            return $currencies->has($key)
                ? $currencies->get($key)['title']
                : $key;
        }

        return (string)$key;
    }

    /**
     * @param string|null $key
     * @return string
     */
    public function getSign(?string $key): string
    {
        $currencies = $this->getCurrencyCollection();

        if ($key && $currencies) {
            return $currencies->has($key)
                ? $currencies->get($key)['sign']
                : "";
        }

        return "";
    }

    /**
     * @param string|null $key
     * @return string|null
     */
    public function getCodeByCountry(?string $key): ?string
    {
        $countries = collect(config('countries'));

        if ($key && $countries) {
            return $countries->has($key)
                ? $countries->get($key)['currency']['code']
                : null;
        }

        return null;
    }

    /**
     * @return Collection|null
     */
    private function getCurrencyCollection(): ?Collection
    {
        $collection = collect(config('countries'));

        if ($collection->isNotEmpty()) {
            $countryLocale = array_key_exists(app()->getLocale(), $collection->first()['currency']['locale'])
                ? app()->getLocale()
                : config('app.fallback_locale');

            $result = collect();

            $collection->each(function ($_item) use ($countryLocale, $result) {
                $result->put($_item['currency']['code'], [
                    'title' => $_item['currency']['locale'][$countryLocale],
                    'sign' => $_item['currency']['sign'],
                ]);
            });

            return $result;
        }

        return null;
    }
}

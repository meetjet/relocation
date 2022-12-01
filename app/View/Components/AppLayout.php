<?php

namespace App\View\Components;

use App\Facades\Countries;
use App\Models\ListingCategory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Request;
use Illuminate\View\Component;

class AppLayout extends Component
{
    public ?array $countries;
    public ?string $activeCountry;

    /**
     * Get the view / contents that represents the component.
     *
     * @return Application|Factory|View
     */
    public function render(): Application|Factory|View
    {
        $file = file_get_contents(base_path('menu.json'));

        $listingCategories = Request::route()->named('listings.index', 'listings.category', 'listings.show', 'listings-by-tag.index')
            ? ListingCategory::active()->orderBy('id')->get()
            : null;

        //<editor-fold desc="Country switch">
        $currentUrl = url()->current();
        $currentCountry = str(request()->getHost())
            ->replace(('.' . config('app.domain')), "")
            ->lower()
            ->value();
        $countryValues = Countries::getValues();

        $this->activeCountry = in_array($currentCountry, $countryValues, true)
            ? Countries::getDescription($currentCountry)
            : __('Choose a country');

        $this->countries = Arr::map(Countries::asSelectArray(), static function ($_countryName, $_countrySlug) use ($currentUrl, $currentCountry, $countryValues) {
            $url = in_array($currentCountry, $countryValues, true)
                ? str($currentUrl)->replace($currentCountry, $_countrySlug)
                : addSubdomainToUrl($currentUrl, $_countrySlug);

            return [
                'name' => $_countryName,
                'is_active' => ($_countrySlug === $currentCountry),
                'url' => $url,
            ];
        });
        //</editor-fold>

        return view('layouts.app', [
            'menu' => json_decode($file),
            'listingCategories' => $listingCategories,
        ]);
    }
}

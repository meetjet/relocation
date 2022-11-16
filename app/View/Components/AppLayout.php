<?php

namespace App\View\Components;

use App\Models\ListingCategory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;
use Illuminate\View\Component;

class AppLayout extends Component
{
    public string $activeCountry = 'Выберите страну';
    public array $countries = [
        'armenia' => 'Армения',
        'georgia' => 'Грузия',
        'turkey' => 'Турция',
        'thailand' => 'Таиланд'
    ];

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

        $this->setCountry();

        return view('layouts.app', [
            'countries' => $this->countries,
            'activeCountry' => $this->activeCountry,
            'menu' => json_decode($file),
            'listingCategories' => $listingCategories,
        ]);
    }

    public function setCountry()
    {
        $subdomain = explode('.', $_SERVER['HTTP_HOST'])[0];

        foreach ($this->countries as $key => $value) {
            if ($key === $subdomain) {
                $this->activeCountry = $this->countries[$subdomain];
                break;
            }
        }
    }
}

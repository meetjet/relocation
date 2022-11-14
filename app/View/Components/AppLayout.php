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
    /**
     * Get the view / contents that represents the component.
     *
     * @return Application|Factory|View
     */
    public function render(): Application|Factory|View
    {
        $file = file_get_contents(base_path('menu.json'));

        $listingCategories = Request::route()->named('listings.index', 'listings.show')
            ? ListingCategory::active()->orderBy('title')->get()
            : null;

        return view('layouts.app', [
            'menu' => json_decode($file),
            'listingCategories' => $listingCategories,
        ]);
    }
}

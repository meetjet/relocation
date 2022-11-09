<?php

namespace App\View\Components;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Component;

class AppLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $menu = json_decode(file_get_contents(base_path('menu.json')), true);

        $country = Route::current()->parameter('country');
        $menu = Arr::map($menu, static function ($_item) use ($country) {
            $_item['route_params'] = [
                'country' => $country,
            ];
            return $_item;
        });

        return view('layouts.app', [
            'menu' => $menu,
        ]);
    }
}

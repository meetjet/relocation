<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class ListingItemByTagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param string $tag
     * @return Application|Factory|View
     */
    public function index(string $tag): Application|Factory|View
    {
        return view('listings.by-tag-index', [
            'tag' => $tag,
        ]);
    }
}

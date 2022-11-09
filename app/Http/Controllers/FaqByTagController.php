<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class FaqByTagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param string $country
     * @param string $tag
     * @return Application|Factory|View
     */
    public function index(string $country, string $tag): Application|Factory|View
    {
        return view('faqs.by-tag-index', [
            'tag' => $tag,
        ]);
    }
}

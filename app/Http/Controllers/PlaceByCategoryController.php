<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\PlaceCategory;
use App\Models\ListingItem;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class PlaceByCategoryController extends Controller
{
    /**
     * Display a index of the resource.
     *
     * @param string $category
     * @return Application|Factory|View
     */
    public function index(string $category): Application|Factory|View
    {
        $entity = PlaceCategory::bySlug($category)->first();

        abort_unless(!is_null($entity), 404);

        return view('events.by-category-index', [
            'category' => $entity,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param string $category
     * @param string $uuid
     * @return Application|Factory|View
     */
    public function show(string $category, string $slug): Application|Factory|View
    {
        $entity = Event::active()
            ->BySlug($slug)
            ->whereHas('category', function (Builder $query) use ($category) {
                $query->where('slug', $category);
            })
            ->with(['tags', 'pictures', 'category'])
            ->first();

        abort_unless(!is_null($entity), 404);

        return view('places.show', [
            'entity' => $entity,
        ]);
    }
}

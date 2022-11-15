<?php

namespace App\Http\Controllers;

use App\Models\ListingCategory;
use App\Models\ListingItem;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class ListingItemByCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param string $category
     * @return Application|Factory|View
     */
    public function index(string $category): Application|Factory|View
    {
        $entity = ListingCategory::bySlug($category)->first();

        abort_unless(!is_null($entity), 404);

        return view('listings.by-category-index', [
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
    public function show(string $category, string $uuid): Application|Factory|View
    {
        $entity = ListingItem::active()
            ->ByUUID($uuid)
            ->whereHas('category', function (Builder $query) use ($category) {
                $query->where('slug', $category);
            })
            ->with(['tags', 'pictures', 'category'])
            ->first();

        abort_unless(!is_null($entity), 404);

        return view('listings.show', [
            'entity' => $entity,
        ]);
    }
}

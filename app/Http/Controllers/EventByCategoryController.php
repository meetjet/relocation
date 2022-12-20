<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\ListingItem;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class EventByCategoryController extends Controller
{
    /**
     * Display a index of the resource.
     *
     * @param string $category
     * @return Application|Factory|View
     */
    public function index(string $category): Application|Factory|View
    {
        $entity = EventCategory::bySlug($category)->first();

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
    public function show(string $category, string $uuid): Application|Factory|View
    {
        $entity = Event::active()
            ->ByUUID($uuid)
            ->whereHas('category', function (Builder $query) use ($category) {
                $query->where('slug', $category);
            })
            ->with(['tags', 'pictures', 'category'])
            ->first();

        abort_unless(!is_null($entity), 404);

        // Get a template for the "Learn more" button.
        $moreTemplate = config('filament.rich_editor_more_template');

        $entity->description = str($entity->description)
            ->replace(["<p>{$moreTemplate}</p>", $moreTemplate], ["", ""])
            ->trim()
            ->value();

        return view('events.show', [
            'entity' => $entity,
        ]);
    }
}

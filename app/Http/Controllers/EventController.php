<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(): Application|Factory|View
    {
        return view('events.index');
    }

    /**
     * Display the specified resource.
     *
     * @param string $uuid
     * @return Application|Factory|View
     */
    public function show(string $uuid): Application|Factory|View
    {
        $entity = Event::active()
            ->ByUUID($uuid)
            ->with(['tags', 'pictures', 'category', 'point'])
            ->first();

        abort_unless(!is_null($entity), 404);

        return view('events.show', [
            'entity' => $entity,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\ListingItem;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ListingItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(): Application|Factory|View
    {
        return view('listings.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param string $uuid
     * @return Application|Factory|View
     */
    public function show(string $uuid): Application|Factory|View
    {
        try {
            $entity = ListingItem::active()
                ->ByUUID($uuid)
                ->with(['tags', 'pictures', 'category'])
                ->first();
        } catch (\Throwable $th) {
            logger($th->getMessage());
            abort(404);
        }

        abort_unless(!is_null($entity), 404);

//        $entity->pictures = (object)[
//            (object)['thumbnail_square' => 'https://upcdn.io/FW25awg/thumbnail-square/uploads/2022/11/10/HT6gygbi.jpeg', 'caption' => 'caption'],
//            (object)['thumbnail_square' => 'https://upcdn.io/FW25awg/thumbnail-square/uploads/2022/11/10/gnFB1nrs.jpeg', 'caption' => 'caption'],
//            (object)['thumbnail_square' => 'https://upcdn.io/FW25awg/thumbnail-square/uploads/2022/11/10/gnFB1nrs.jpeg', 'caption' => 'caption'],
//            (object)['thumbnail_square' => 'https://upcdn.io/FW25awg/thumbnail-square/uploads/2022/11/10/gnFB1nrs.jpeg', 'caption' => 'caption'],
//            (object)['thumbnail_square' => 'https://upcdn.io/FW25awg/thumbnail-square/uploads/2022/11/10/gnFB1nrs.jpeg', 'caption' => 'caption'],
//            (object)['thumbnail_square' => 'https://upcdn.io/FW25awg/thumbnail-square/uploads/2022/11/10/gnFB1nrs.jpeg', 'caption' => 'caption'],
//        ];

        return view('listings.show', [
            'entity' => $entity,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

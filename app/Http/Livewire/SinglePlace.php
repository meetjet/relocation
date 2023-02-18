<?php

namespace App\Http\Livewire;

use App\Models\Place;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class SinglePlace extends Component
{
    public Place $entity;

    /**
     * @return Application|Factory|View
     */
    public function render(): Application|Factory|View
    {
        return view('livewire.single-place', [
            'entity' => $this->entity,
        ]);
    }
}

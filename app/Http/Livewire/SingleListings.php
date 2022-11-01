<?php

namespace App\Http\Livewire;

use App\Models\ListingItem;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class SingleListings extends Component
{
    public ListingItem $entity;

    /**
     * @return Application|Factory|View
     */
    public function render(): Application|Factory|View
    {
        return view('livewire.single-listings', [
            'entity' => $this->entity,
        ]);
    }
}

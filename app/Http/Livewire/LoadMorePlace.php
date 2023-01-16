<?php

namespace App\Http\Livewire;

use App\Models\Place;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class LoadMorePlace extends Component
{
    public int $total = -1;
    public int $perPage = 12;

    protected $listeners = [
        'load-more-place' => 'loadMorePlace'
    ];

    public function loadMorePlace(): void
    {
        $this->perPage += 12;
    }

    /**
     * @return Application|Factory|View
     */
    public function render(): Application|Factory|View
    {
        if ($this->total === -1) {
            $this->total = Place::active()->count();
        }

        $items = Place::active()->paginate($this->perPage);

        $items->each(function ($_item) {
            $_item->cover_picture = $_item->firstPicture()->first();
        });

        $this->emit('PlacesStore');

        return view('livewire.load-more-place', [
            'items' => $items,
            'total' => $this->total,
        ]);
    }
}

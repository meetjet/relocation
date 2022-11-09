<?php

namespace App\Http\Livewire;

use App\Models\ListingItem;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;
use Livewire\Component;

class LoadMoreListings extends Component
{
    public int $total = -1;
    public int $perPage = 10;
    public ?string $country = null;

    protected $listeners = [
        'listings-load-more' => 'listingsLoadMore'
    ];

    public function listingsLoadMore(string $country): void
    {
        $this->perPage += 10;
        $this->country = $country;
    }

    /**
     * @return Application|Factory|View
     */
    public function render(): Application|Factory|View
    {
        $this->country = $this->country ?: Route::current()->parameter('country');

        if ($this->total === -1) {
            $this->total = ListingItem::active()
                ->count();
        }

        $items = ListingItem::active()
            ->latest()
            ->paginate($this->perPage);

        $items->each(function ($_item) {
            $_item->cover_picture = $_item->firstPicture()->first();
        });

        $this->emit('listingsStore');

        return view('livewire.load-more-listings', [
            'items' => $items,
            'total' => $this->total,
        ]);
    }
}

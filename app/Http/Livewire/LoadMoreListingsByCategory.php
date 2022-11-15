<?php

namespace App\Http\Livewire;

use App\Models\ListingCategory;
use App\Models\ListingItem;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class LoadMoreListingsByCategory extends Component
{
    public ListingCategory $category;
    public int $total = -1;
    public int $perPage = 12;

    protected $listeners = [
        'listings-load-more' => 'listingsLoadMore'
    ];

    public function listingsLoadMore(): void
    {
        $this->perPage += 12;
    }

    /**
     * @return Application|Factory|View
     */
    public function render(): Application|Factory|View
    {
        if ($this->total === -1) {
            $this->total = ListingItem::active()
                ->whereHas('category', function (Builder $query) {
                    $query->where('slug', $this->category->slug);
                })
                ->count();
        }

        $items = ListingItem::active()
            ->whereHas('category', function (Builder $query) {
                $query->where('slug', $this->category->slug);
            })
            ->latest()
            ->paginate($this->perPage);

        $items->each(function ($_item) {
            $_item->cover_picture = $_item->firstPicture()->first();
        });

        $this->emit('listingsStore'); // TODO: ?!

        return view('livewire.load-more-listings-by-category', [
            'items' => $items,
        ]);
    }
}

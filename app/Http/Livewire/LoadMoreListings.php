<?php

namespace App\Http\Livewire;

use App\Models\ListingItem;
use App\Models\Picture;
use App\UploadIO\UploadIO;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class LoadMoreListings extends Component
{
    public int $total = -1;
    public int $perPage = 10;

    protected $listeners = [
        'listings-load-more' => 'listingsLoadMore'
    ];

    public function listingsLoadMore(): void
    {
        $this->perPage += 10;
    }

    /**
     * @param UploadIO $uploadIO
     * @return Application|Factory|View
     */
    public function render(UploadIO $uploadIO): Application|Factory|View
    {
        if ($this->total === -1) {
            $this->total = ListingItem::active()->count();
        }

        $items = ListingItem::active()
            ->latest()
            ->paginate($this->perPage);

        $items->each(function ($_item) use ($uploadIO) {
            // Get a cover image.
            /** @var Picture $picture */
            $picture = $_item->pictures()->cover()->first();

            $_item->cover_image = $picture ? [
//                'url' => $picture->transform("resize-200-crop-center"), // TODO: if transformation is required
                'url' => $picture->url,
                'caption' => $picture->caption,
            ] : null;
        });

        $this->emit('listingsStore');

        return view('livewire.load-more-listings', [
            'items' => $items,
            'total' => $this->total,
        ]);
    }
}

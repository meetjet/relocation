<?php

namespace App\Http\Livewire;

use App\Models\ListingItem;
use Livewire\Component;

class LoadMoreListingItems extends Component
{
    public int $total = -1;
    public int $perPage = 10;

    protected $listeners = [
        'listing-items-load-more' => 'listingItemsLoadMore'
    ];

    public function listingItemsLoadMore(): void
    {
        $this->perPage += 10;
    }

    public function render()
    {
        if ($this->total === -1) {
            $this->total = ListingItem::query()
                ->where('status', 'published')
                ->where('visibility', true)
                ->count();
        }

        $items = ListingItem::query()
            ->where('status', 'published')
            ->where('visibility', true)
            ->latest()->paginate($this->perPage);
        $this->emit('listingItemsStore');

        $images = [
            "https://balance.designmyshop.ru/wa-data/public/shop/products/37/00/37/images/309/309.200.jpg",
            "https://balance.designmyshop.ru/wa-data/public/shop/products/52/00/52/images/355/355.200.jpg",
            "https://balance.designmyshop.ru/wa-data/public/shop/products/55/00/55/images/378/378.200.jpg",
            "https://balance.designmyshop.ru/wa-data/public/shop/products/37/00/37/images/309/309.200.jpg",
            "https://balance.designmyshop.ru/wa-data/public/shop/products/52/00/52/images/355/355.200.jpg",
            "https://balance.designmyshop.ru/wa-data/public/shop/products/55/00/55/images/378/378.200.jpg",
            "https://balance.designmyshop.ru/wa-data/public/shop/products/37/00/37/images/309/309.200.jpg",
            "https://balance.designmyshop.ru/wa-data/public/shop/products/52/00/52/images/355/355.200.jpg",
            "https://balance.designmyshop.ru/wa-data/public/shop/products/55/00/55/images/378/378.200.jpg",
            "https://balance.designmyshop.ru/wa-data/public/shop/products/37/00/37/images/309/309.200.jpg",
            "https://balance.designmyshop.ru/wa-data/public/shop/products/52/00/52/images/355/355.200.jpg",
            "https://balance.designmyshop.ru/wa-data/public/shop/products/55/00/55/images/378/378.200.jpg",
            "https://balance.designmyshop.ru/wa-data/public/shop/products/37/00/37/images/309/309.200.jpg",
            "https://balance.designmyshop.ru/wa-data/public/shop/products/52/00/52/images/355/355.200.jpg",
            "https://balance.designmyshop.ru/wa-data/public/shop/products/55/00/55/images/378/378.200.jpg",
            "https://balance.designmyshop.ru/wa-data/public/shop/products/55/00/55/images/378/378.200.jpg",
            "https://balance.designmyshop.ru/wa-data/public/shop/products/37/00/37/images/309/309.200.jpg",
            "https://balance.designmyshop.ru/wa-data/public/shop/products/52/00/52/images/355/355.200.jpg",
            "https://balance.designmyshop.ru/wa-data/public/shop/products/55/00/55/images/378/378.200.jpg",
            "https://balance.designmyshop.ru/wa-data/public/shop/products/37/00/37/images/309/309.200.jpg",
            "https://balance.designmyshop.ru/wa-data/public/shop/products/52/00/52/images/355/355.200.jpg",
            "https://balance.designmyshop.ru/wa-data/public/shop/products/55/00/55/images/378/378.200.jpg",
            "https://balance.designmyshop.ru/wa-data/public/shop/products/37/00/37/images/309/309.200.jpg",
            "https://balance.designmyshop.ru/wa-data/public/shop/products/52/00/52/images/355/355.200.jpg",
            "https://balance.designmyshop.ru/wa-data/public/shop/products/55/00/55/images/378/378.200.jpg",
            "https://balance.designmyshop.ru/wa-data/public/shop/products/37/00/37/images/309/309.200.jpg",
            "https://balance.designmyshop.ru/wa-data/public/shop/products/52/00/52/images/355/355.200.jpg",
            "https://balance.designmyshop.ru/wa-data/public/shop/products/55/00/55/images/378/378.200.jpg",
            "https://balance.designmyshop.ru/wa-data/public/shop/products/37/00/37/images/309/309.200.jpg",
            "https://balance.designmyshop.ru/wa-data/public/shop/products/52/00/52/images/355/355.200.jpg",
            "https://balance.designmyshop.ru/wa-data/public/shop/products/55/00/55/images/378/378.200.jpg",
            "https://balance.designmyshop.ru/wa-data/public/shop/products/55/00/55/images/378/378.200.jpg"
        ];

        return view('livewire.load-more-listing-items', ['items' => $items, 'total' => $this->total, 'images' => $images]);
    }
}

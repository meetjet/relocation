<?php

namespace App\Http\Livewire;

use App\Models\ListingItem;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Spatie\Tags\Tag;

class LoadMoreListingsByTag extends Component
{
    public string $tag;
    public int $total = -1;
    public int $perPage = 12;
    public ?string $botUsername;

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
        $locale = app()->getLocale();

        $tag = Tag::query()
            ->where("slug->{$locale}", $this->tag)
            ->where('type', "listing-items")
            ->first();

        abort_unless(!is_null($tag), 404);

        if ($this->total === -1) {
            $this->total = ListingItem::active()
                ->withAnyTags($tag)
                ->count();
        }

        $currentCounty = getCurrentCountry();
        $this->botUsername = $currentCounty
            ? config("nutgram.username_{$currentCounty}")
            : null;

        $items = ListingItem::active()
            ->withAnyTags($tag)
            ->latest()
            ->paginate($this->perPage);

        $items->each(function ($_item) {
            $_item->cover_picture = $_item->firstPicture()->first();
        });

        $this->emit('listingsStore');

        return view('livewire.load-more-listings-by-tag', [
            'items' => $items,
            'total' => $this->total,
            'currentTag' => $this->tag,
        ]);
    }
}

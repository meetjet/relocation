<?php

namespace App\Http\Livewire;

use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class LoadMoreEventByCategory extends Component
{
    public EventCategory $category;
    public int $total = -1;
    public int $perPage = 12;

    protected $listeners = [
        'load-more-event' => 'loadMoreEvent'
    ];

    public function loadMoreEvent(): void
    {
        $this->perPage += 12;
    }

    /**
     * @return Application|Factory|View
     */
    public function render(): Application|Factory|View
    {
        if ($this->total === -1) {
            $this->total = Event::active()
                ->startOfCurrentWeek()
                ->whereHas('category', function (Builder $query) {
                    $query->where('slug', $this->category->slug);
                })
                ->count();
        }

        $items = Event::active()
            ->startOfCurrentWeek()
            ->whereHas('category', function (Builder $query) {
                $query->where('slug', $this->category->slug);
            })
            ->orderByStartDate()
            ->paginate($this->perPage);

        $items->each(function ($_item) {
            $_item->cover_picture = $_item->firstPicture()->first();
        });

        $this->emit('EventsStore');

        return view('livewire.load-more-event-by-category', [
            'items' => $items,
        ]);
    }
}

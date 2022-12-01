<?php

namespace App\Http\Livewire;

use App\Models\Event;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class LoadMoreEvent extends Component
{
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
            $this->total = Event::active()->count();
        }

        $items = Event::active()
            ->latest()
            ->paginate($this->perPage);

        $items->each(function ($_item) {
            $_item->cover_picture = $_item->firstPicture()->first();
        });

        $this->emit('EventsStore');

        return view('livewire.load-more-event', [
            'items' => $items,
            'total' => $this->total,
        ]);
    }
}

<?php

namespace App\Http\Livewire;

use App\Models\Event;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Spatie\Tags\Tag;

class LoadMoreEventByTag extends Component
{
    public string $tag;
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
        $locale = app()->getLocale();

        $tag = Tag::query()
            ->where("slug->{$locale}", $this->tag)
            ->where('type', "events")
            ->first();

        abort_unless(!is_null($tag), 404);

        if ($this->total === -1) {
            $this->total = Event::active()
                ->startOfCurrentWeek()
                ->withAnyTags($tag)
                ->count();
        }

        $items = Event::active()
            ->startOfCurrentWeek()
            ->withAnyTags($tag)
            ->orderByStartDate()
            ->paginate($this->perPage);

        $items->each(function ($_item) {
            $_item->cover_picture = $_item->firstPicture()->first();
        });

        $this->emit('EventsStore');

        return view('livewire.load-more-event-by-tag', [
            'items' => $items,
            'total' => $this->total,
            'currentTag' => $this->tag,
        ]);
    }
}

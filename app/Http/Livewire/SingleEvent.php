<?php

namespace App\Http\Livewire;

use App\Models\Event;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class SingleEvent extends Component
{
    public Event $entity;

    /**
     * @return Application|Factory|View
     */
    public function render(): Application|Factory|View
    {
        return view('livewire.single-event', [
            'entity' => $this->entity,
        ]);
    }
}

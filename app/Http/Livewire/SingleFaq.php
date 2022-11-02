<?php

namespace App\Http\Livewire;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use App\Models\Faq;

class SingleFaq extends Component
{
    public Faq $entity;

    public function render(): Application|Factory|View
    {
        return view('livewire.single-faq', [
            'entity' => $this->entity,
        ]);
    }
}

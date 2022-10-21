<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Faq;

class SingleFaq extends Component
{
    public $entity;

    public function render()
    {
        return view('livewire.single-faq', ['entity' => $this->entity]);
    }
}

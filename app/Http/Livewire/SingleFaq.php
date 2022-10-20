<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Faq;

class SingleFaq extends Component
{

    public function render()
    {
        [$faq] = Faq::query()
            ->where('status', 'published')
            ->where('visibility', true)
            ->latest()->paginate(1);;

        return view('livewire.single-faq', ['faq' => $faq]);
    }
}

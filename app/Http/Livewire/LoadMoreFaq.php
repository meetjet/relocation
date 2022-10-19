<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Faq;

class LoadMoreFaq extends Component
{
    public $perPage = 15;
    protected $listeners = [
        'load-more' => 'loadMore'
    ];

    /**
     * Write code on Method
     */
    public function loadMore()
    {
        $this->perPage = $this->perPage + 5;
    }

    /**
     * Write code on Method
     */
    public function render()
    {
        $faqs = Faq::latest()->paginate($this->perPage);
        $this->emit('userStore');

        return view('livewire.load-more-faq', ['faqs' => $faqs]);
    }
}

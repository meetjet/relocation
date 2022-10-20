<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Faq;

class LoadMoreFaq extends Component
{
    public $perPage = 15;
    protected $listeners = [
        'faqs-load-more' => 'faqsLoadMore'
    ];

    public function faqsLoadMore(): void
    {
        $this->perPage += 5;
    }

    public function render()
    {
        $faqs = Faq::query()
            ->where('status', 'published')
            ->where('visibility', true)
            ->latest()->paginate($this->perPage);
        $this->emit('faqStore');

        return view('livewire.load-more-faq', ['faqs' => $faqs]);
    }
}

<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Faq;

class LoadMoreFaq extends Component
{
    public int $total = -1;
    public int $perPage = 5;

    protected $listeners = [
        'faqs-load-more' => 'faqsLoadMore'
    ];

    public function faqsLoadMore(): void
    {
        $this->perPage += 5;
    }

    public function render()
    {
        if ($this->total === -1) {
            $this->total = Faq::query()
                ->where('status', 'published')
                ->where('visibility', true)
                ->count();
        }

        $faqs = Faq::query()
            ->where('status', 'published')
            ->where('visibility', true)
            ->latest()->paginate($this->perPage);
        $this->emit('faqStore');

        return view('livewire.load-more-faq', ['faqs' => $faqs, 'total' => $this->total]);
    }
}

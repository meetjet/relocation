<?php

namespace App\Http\Livewire;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
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

    /**
     * @return Application|Factory|View
     */
    public function render(): Application|Factory|View
    {
        if ($this->total === -1) {
            $this->total = Faq::active()->count();
        }

        $faqs = Faq::active()
            ->latest()
            ->paginate($this->perPage);

        $this->emit('faqStore');

        return view('livewire.load-more-faq', [
            'faqs' => $faqs,
            'total' => $this->total,
        ]);
    }
}

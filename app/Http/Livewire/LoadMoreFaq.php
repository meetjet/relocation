<?php

namespace App\Http\Livewire;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;
use Livewire\Component;
use App\Models\Faq;

class LoadMoreFaq extends Component
{
    public int $total = -1;
    public int $perPage = 10;
    public ?string $country = null;

    protected $listeners = [
        'faqs-load-more' => 'faqsLoadMore'
    ];

    public function faqsLoadMore(string $country): void
    {
        $this->perPage += 10;
        $this->country = $country;
    }

    /**
     * @return Application|Factory|View
     */
    public function render(): Application|Factory|View
    {
        $this->country = $this->country ?: Route::current()->parameter('country');

        if ($this->total === -1) {
            $this->total = Faq::active()
                ->count();
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

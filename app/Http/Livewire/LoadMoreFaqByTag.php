<?php

namespace App\Http\Livewire;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use App\Models\Faq;
use Spatie\Tags\Tag;

class LoadMoreFaqByTag extends Component
{
    public string $tag;
    public int $total = -1;
    public int $perPage = 10;

    protected $listeners = [
        'faqs-load-more' => 'faqsLoadMore'
    ];

    public function faqsLoadMore(): void
    {
        $this->perPage += 10;
    }

    /**
     * @return Application|Factory|View
     */
    public function render(): Application|Factory|View
    {
        $locale = app()->getLocale();

        $tag = Tag::query()
            ->where("slug->{$locale}", $this->tag)
            ->where('type', "faqs")
            ->first();

        if ($this->total === -1) {
            $this->total = Faq::active()
                ->withAnyTags($tag)
                ->count();
        }

        $faqs = Faq::active()
            ->withAnyTags($tag)
            ->latest()
            ->paginate($this->perPage);

        $this->emit('faqStore');

        return view('livewire.load-more-faq-by-tag', [
            'total' => $this->total,
            'faqs' => $faqs,
            'currentTag' => $this->tag,
        ]);
    }
}

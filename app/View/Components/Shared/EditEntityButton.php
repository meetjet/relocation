<?php

namespace App\View\Components\Shared;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class EditEntityButton extends Component
{
    public string $url;

    /**
     * Create a new component instance.
     *
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return Application|Factory|View
     */
    public function render(): Application|Factory|View
    {
        return view('components.shared.edit-entity-button');
    }
}

<?php

namespace App\Http\Livewire;

use App\Models\Event;
use Filament\Forms;
use Illuminate\Support\Facades\Request;
use Illuminate\View\View;
use Livewire\Component;

class AddEventForm extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    public $title = '';
    public $description = '';
    public $user_id = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('title')->required(),
            Forms\Components\RichEditor::make('description'),
            Forms\Components\Hidden::make('user_id')
                ->default(function () {
                    $user = Request::user();
                    return $user->id;
                })
                ->disabled(),
        ];
    }

    public function submit(): void
    {
        Event::create($this->form->getState());
    }

    public function render(): View
    {
        return view('livewire.add-event-form');
    }

    protected function getFormModel(): string
    {
        return Event::class;
    }
}

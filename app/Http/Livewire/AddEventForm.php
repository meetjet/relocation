<?php

namespace App\Http\Livewire;

use App\Enums\EventPaymentType;
use App\Facades\Currencies;
use App\Facades\Locations;
use App\Models\Event;
use Closure;
use Filament\Forms;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class AddEventForm extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    public function mount(): void
    {
        $this->form->fill();
    }

    /**
     * @return array
     */
    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->schema($this->getFormSchema())
                ->columns(5)
                ->model($this->getFormModel())
                ->statePath($this->getFormStatePath())
                ->context($this->getFormContext()),
        ];
    }

    /**
     * @return array
     */
    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make(__('Event'))
                ->extraAttributes(['class' => 'bg-gray-50'])
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label(__('Title'))
                        ->maxLength(255)
                        ->autofocus()
                        ->required(),

                    Forms\Components\RichEditor::make('description')
                        ->label(__('Description'))
                        ->disableToolbarButtons([
                            'attachFiles',
                            'codeBlock',
                        ])
                        ->nullable(),
                ])
                ->columnSpan([
                    '2xl' => 4,
                ])
                ->collapsible(),

            Forms\Components\Section::make(__('Event where'))
                ->extraAttributes(['class' => 'bg-gray-50'])
                ->schema([
                    Forms\Components\Select::make('location')
                        ->label(__('Location'))
                        ->options(Locations::asSelectArray(getCurrentCountry()))
                        ->placeholder("-")
                        ->reactive()
                        ->afterStateUpdated(function (Closure $set) {
                            $set('place_slug', "");
                        })
                        ->columnSpan([
                            'default' => 2,
                            'lg' => 1,
                        ])
                        ->nullable(),

                    Forms\Components\Select::make('place_slug')
                        ->label(__('Place'))
                        ->relationship(
                            'place',
                            'title',
                            function (Builder $query, Closure $get) {
                                return $query->where('country', $get('country'))
                                    ->where('location', $get('location'))
                                    ->orderBy('id');
                            }
                        )
                        ->placeholder("-")
                        ->columnSpan([
                            'default' => 2,
                            'lg' => 1,
                        ])
                        ->nullable(),

                    Forms\Components\TextInput::make('address')
                        ->label(__('Address'))
                        ->columnSpan(2)
                        ->nullable(),

                    Forms\Components\Hidden::make('country')
                        ->default(getCurrentCountry())
                        ->required(),
                ])
                ->columns()
                ->columnSpan([
                    '2xl' => 4,
                ])
                ->collapsible(),

            Forms\Components\Section::make(__('Event when'))
                ->extraAttributes(['class' => 'bg-gray-50'])
                ->schema([
                    Forms\Components\DatePicker::make('start_date')
                        ->label(__('Start date'))
                        ->displayFormat("j M Y")
                        ->required(),

                    Forms\Components\TimePicker::make('start_time')
                        ->label(__('Start time'))
                        ->withoutSeconds()
                        ->nullable(),

                    Forms\Components\DatePicker::make('finish_date')
                        ->label(__('Finish date'))
                        ->displayFormat("j M Y")
                        ->requiredWith('finish_time'),

                    Forms\Components\TimePicker::make('finish_time')
                        ->label(__('Finish time'))
                        ->withoutSeconds()
                        ->nullable(),
                ])
                ->columns()
                ->columnSpan([
                    '2xl' => 4,
                ])
                ->collapsible(),

            Forms\Components\Section::make(__('Event payment'))
                ->extraAttributes(['class' => 'bg-gray-50'])
                ->schema([
                    Forms\Components\Select::make('payment_type')
                        ->label(__('Payment type'))
                        ->placeholder("-")
                        ->options(EventPaymentType::asSelectArray())
                        ->reactive()
                        ->required(),

                    Forms\Components\TextInput::make('price')
                        ->label(__('Price'))
                        ->numeric()
                        ->minValue(1)
                        ->visible(fn(Closure $get) => $get('payment_type') === EventPaymentType::PAID)
                        ->required(),

                    Forms\Components\Select::make('currency')
                        ->label(__('Currency'))
                        ->hint(__('Automatic selection'))
                        ->disablePlaceholderSelection()
                        ->options(Currencies::asSelectArray())
                        ->default(Currencies::getCodeByCountry(getCurrentCountry()))
                        ->visible(fn(Closure $get) => $get('payment_type') === EventPaymentType::PAID)
                        ->disabled()
                        ->required(),
                ])
                ->columns(3)
                ->columnSpan([
                    '2xl' => 4,
                ])
                ->collapsible(),

            Forms\Components\Section::make(__('Event organizer'))
                ->extraAttributes(['class' => 'bg-gray-50'])
                ->schema([
                    Forms\Components\TextInput::make('phone')
                        ->label(__('Phone'))
                        ->requiredWithout(function () {
                            $user = Request::user();

                            return $user && $user->contact
                                ? null
                                : 'email';
                        }),

                    Forms\Components\TextInput::make('email')
                        ->label(__('Email'))
                        ->email()
                        ->requiredWithout(function () {
                            $user = Request::user();

                            return $user && $user->contact
                                ? null
                                : 'phone';
                        }),

                    Forms\Components\Placeholder::make('nickname')
                        ->label(__('Your Telegram account'))
                        ->content(function () {
                            $user = Request::user();

                            return $user && $user->contact
                                ? new HtmlString('<a href="https://t.me/' . $user->contact->nickname . '" class="text-blue-600" target="_blank">@' . $user->contact->nickname . '</a>')
                                : null;
                        })
                        ->visible(function () {
                            $user = Request::user();

                            return $user && $user->contact;
                        }),
                ])
                ->columns()
                ->columnSpan([
                    '2xl' => 4,
                ])
                ->collapsible(),

            Forms\Components\Hidden::make('user_id')
                ->default(function () {
                    $user = Request::user();

                    return $user
                        ? $user->id
                        : 1; // TODO: as admin
                })
                ->disabled(),
        ];
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        // Remove redundant line breaks.
        if ($data['description']) {
            $data['description'] = strReplace("<br><br><br>", "<br><br>", $data['description']);
        }

        Event::create($data);

        Notification::make()
            ->title(__('Event sent successfully'))
            ->success()
            ->send();

        $this->form->fill();
    }

    /**
     * @return View
     */
    public function render(): View
    {
        return view('livewire.add-event-form');
    }

    /**
     * @return string
     */
    protected function getFormModel(): string
    {
        return Event::class;
    }
}

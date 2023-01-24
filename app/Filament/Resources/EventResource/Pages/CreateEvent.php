<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Enums\EventPaymentType;
use App\Enums\EventStatus;
use App\Facades\Currencies;
use App\Facades\Locations;
use App\Facades\Countries;
use App\Filament\Resources\EventResource;
use App\Forms\Components\RichEditor;
use Closure;
use Filament\Forms\Components;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

/**
 * @package App\Filament\Resources\EventResource\Pages
 */
class CreateEvent extends CreateRecord
{
    protected static string $resource = EventResource::class;

    /**
     * @return array
     */
    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->context('create')
                ->model($this->getModel())
                ->schema($this->getFormSchema())
                ->columns(3)
                ->statePath('data')
                ->inlineLabel(config('filament.layout.forms.have_inline_labels')),
        ];
    }

    /**
     * @return array
     */
    protected function getFormSchema(): array
    {
        return [
            Components\Group::make()
                ->schema([
                    Components\Card::make()
                        ->schema([
                            Components\Textarea::make('title')
                                ->label(__('Title'))
                                ->rows(2)
                                ->required()
                                ->autofocus(),

                            RichEditor::make('description')
                                ->label(__('Description'))
                                ->disableToolbarButtons([
                                    'attachFiles',
                                    'codeBlock',
                                ])
                                ->nullable(),

                            Components\SpatieTagsInput::make('tags')
                                ->label(__('Tags'))
                                ->type("events"),

                            Components\Grid::make(3)
                                ->schema([
                                    Components\Select::make('payment_type')
                                        ->label(__('Payment type'))
                                        ->placeholder("-")
                                        ->options(EventPaymentType::asSelectArray())
                                        ->reactive()
                                        ->nullable(),

                                    Components\TextInput::make('price')
                                        ->label(__('Price'))
                                        ->numeric()
                                        ->minValue(1)
                                        ->visible(fn(Closure $get) => $get('payment_type') === EventPaymentType::PAID)
                                        ->required(),

                                    Components\Select::make('currency')
                                        ->label(__('Currency'))
                                        ->hint(__('Automatic selection'))
                                        ->placeholder("-")
                                        ->options(Currencies::asSelectArray())
                                        ->visible(fn(Closure $get) => $get('payment_type') === EventPaymentType::PAID)
                                        ->requiredWith('price')
                                        ->disabled(),
                                ]),

                            Components\Grid::make()
                                ->schema([
                                    Components\DatePicker::make('start_date')
                                        ->label(__('Start date'))
                                        ->displayFormat("j M Y")
                                        ->required(),

                                    Components\TimePicker::make('start_time')
                                        ->label(__('Start time'))
                                        ->withoutSeconds()
                                        ->nullable(),

                                    Components\DatePicker::make('finish_date')
                                        ->label(__('Finish date'))
                                        ->displayFormat("j M Y")
                                        ->requiredWith('finish_time'),

                                    Components\TimePicker::make('finish_time')
                                        ->label(__('Finish time'))
                                        ->withoutSeconds()
                                        ->nullable(),
                                ]),

                            Components\Hidden::make('user_id')
                                ->default(fn(): int => Request::user()->id),
                        ]),

                    Components\Section::make(__('Event owner'))
                        ->schema([
                            Components\TextInput::make('contact.nickname')
                                ->label(__('Real owner nickname'))
                                ->default(function () {
                                    $user = Request::user();

                                    return $user && $user->contact
                                        ? $user->contact->nickname
                                        : null;
                                })
                                ->disabled()
                                ->dehydrated(false),

                            Components\TextInput::make('custom_nickname')
                                ->label(__('Custom nickname'))
                                ->requiredWithoutAll(['contact.nickname', 'email', 'phone']),

                            Components\TextInput::make('email')
                                ->label(__('Owner email'))
//                                ->helperText(__('Requested from the user if he does not have a nickname'))
                                ->email()
                                ->requiredWithoutAll(['contact.nickname', 'custom_nickname', 'phone']),

                            Components\TextInput::make('phone')
                                ->label(__('Owner phone'))
//                                ->helperText(__('Requested from the user if he does not have a nickname'))
                                ->requiredWithoutAll(['contact.nickname', 'custom_nickname', 'email']),
                        ])->columns(),

                    Components\Section::make(__('SEO'))
                        ->schema([
                            Components\TextInput::make('seo.title')
                                ->label(__('SEO title'))
                                ->hint(__('If this field is left blank, it will be filled in automatically'))
                                ->nullable(),

                            Components\Textarea::make('seo.description')
                                ->label(__('SEO description'))
                                ->hint(__('If this field is left blank, it will be filled in automatically'))
                                ->rows(2)
                                ->nullable(),
                        ]),
                ])
                ->columnSpan(['lg' => 2]),

            Components\Group::make()
                ->schema([
                    Components\Card::make()
                        ->schema([
                            Components\Select::make('status')
                                ->label(__('Status'))
                                ->options(EventStatus::asSelectArray())
                                ->disablePlaceholderSelection()
                                ->default(EventStatus::PUBLISHED)
                                ->reactive()
                                ->afterStateUpdated(function (Closure $set, Closure $get) {
                                    if ($get('status') === EventStatus::PUBLISHED) {
                                        $set('published_at', now());
                                    } else {
                                        $set('published_at', null);
                                    }
                                }),

                            Components\DateTimePicker::make('published_at')
                                ->label(__('Published at'))
                                ->displayFormat("j M Y, H:i")
                                ->withoutSeconds()
                                ->default(fn(): string => now()),
                        ]),

                    Components\Card::make()
                        ->schema([
                            Components\Select::make('category_id')
                                ->label(__('Category'))
                                ->relationship(
                                    'category',
                                    'title',
                                    fn(Builder $query): Builder => $query->orderBy('id')
                                )
                                ->placeholder("-")
                                ->required(),

                            Components\Select::make('country')
                                ->label(__('Country'))
                                ->options(Countries::asSelectArray())
                                ->placeholder("-")
                                ->reactive()
                                ->afterStateUpdated(function (Closure $set, Closure $get) {
                                    $set('location', "");
                                    $set('place_slug', "");
                                    $set('currency', Currencies::getCodeByCountry($get('country')));
                                })
                                ->nullable(),

                            Components\Select::make('location')
                                ->label(__('Location'))
                                ->options(fn(Closure $get): array => Locations::asSelectArray($get('country')))
                                ->placeholder("-")
                                ->reactive()
                                ->afterStateUpdated(function (Closure $set) {
                                    $set('place_slug', "");
                                })
                                ->nullable(),

                            Components\Select::make('place_slug')
                                ->label(__('Place'))
                                ->relationship(
                                    'place',
                                    'title',
                                    function (Builder $query, Closure $get) {
                                        $country = $get('country');
                                        $location = $get('location');

                                        if ($country && $location) {
                                            return $query->where('country', $country)
                                                ->where('location', $location)
                                                ->orderBy('id');
                                        }

                                        return $query->where('country', $country)
                                            ->orderBy('id');
                                    }
                                )
                                ->placeholder("-"),
//                                ->requiredWithout('address'),

                            Components\TextInput::make('address')
                                ->label(__('Address')),
//                                ->requiredWithout('place_slug'),
                        ]),
                ])
                ->columnSpan(['lg' => 1]),
        ];
    }

    /**
     * @param array $data
     * @return array
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Remove redundant line breaks.
        if ($data['description']) {
            $data['description'] = strReplace("<br><br><br>", "<br><br>", $data['description']);
        }

        return $data;
    }

    /**
     * @param array $data
     * @return Model
     */
    protected function handleRecordCreation(array $data): Model
    {
        $record = $this->getModel()::create($data);
        $seo = $data['seo'];

        // Get a template for the "Learn more on the site" button.
        $moreTemplate = config('filament.rich_editor_more_template');
        // Replace and strip tags and entities.
        $defaultDescription = str($data['description'])->replace(
            ["<p>{$moreTemplate}</p>", $moreTemplate, "<br>", "</p><p>", "&nbsp;"],
            ["", "", " ", " ", " "]
        );
        $defaultDescription = strip_tags($defaultDescription);

        $record->seo->update([
            'title' => $seo['title'] ?: $data['title'],
            'description' => $seo['description'] ?: str($defaultDescription)->trim()->value(),
        ]);

        return $record;
    }
}

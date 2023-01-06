<?php

namespace App\Filament\Resources\ListingItemResource\Pages;

use App\Enums\ListingItemSource;
use App\Enums\ListingItemStatus;
use App\Facades\Locations;
use App\Facades\Countries;
use App\Facades\Currencies;
use App\Filament\Resources\ListingItemResource;
use Closure;
use Filament\Forms\Components;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class CreateListingItem extends CreateRecord
{
    protected static string $resource = ListingItemResource::class;
    protected static bool $canCreateAnother = false;

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

                            Components\RichEditor::make('description')
                                ->label(__('Description'))
                                ->disableToolbarButtons([
                                    'attachFiles',
                                    'codeBlock',
                                ])
                                ->nullable(),

                            Components\SpatieTagsInput::make('tags')
                                ->label(__('Tags'))
                                ->type("listing-items"),

                            Components\Grid::make()
                                ->schema([
                                    Components\TextInput::make('price')
                                        ->label(__('Price'))
                                        ->numeric()
                                        ->minValue(0)
                                        ->required(),

                                    Components\Select::make('currency')
                                        ->label(__('Currency'))
                                        ->hint(__('Automatic selection'))
                                        ->placeholder("-")
                                        ->options(Currencies::asSelectArray())
//                                        ->default(Currencies::getCodeByCountry("armenia"))
                                        ->disabled(),
                                ]),

                            Components\Hidden::make('user_id')
                                ->default(fn(): int => Request::user()->id),
                        ]),

                    Components\Section::make(__('Announcement owner'))
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
                                ->helperText(__('Requested from the user if he does not have a nickname'))
                                ->email()
                                ->requiredWithoutAll(['contact.nickname', 'custom_nickname', 'phone']),

                            Components\TextInput::make('phone')
                                ->label(__('Owner phone'))
                                ->helperText(__('Requested from the user if he does not have a nickname'))
                                ->requiredWithoutAll(['contact.nickname', 'custom_nickname', 'email']),
                        ])->columns()->collapsible(),

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
                        ])->collapsible(),
                ])
                ->columnSpan(['lg' => 2]),

            Components\Group::make()
                ->schema([
                    Components\Card::make()
                        ->schema([
                            Components\Select::make('status')
                                ->label(__('Status'))
                                ->options(ListingItemStatus::asSelectArray())
                                ->disablePlaceholderSelection()
                                ->default(ListingItemStatus::PUBLISHED)
                                ->reactive()
                                ->afterStateUpdated(function (Closure $set, Closure $get) {
                                    if ($get('status') === ListingItemStatus::PUBLISHED) {
                                        $set('published_at', now());
                                    } else {
                                        $set('published_at', null);
                                    }
                                })
                                ->required(),

                            Components\DateTimePicker::make('published_at')
                                ->label(__('Published at'))
                                ->displayFormat("j M Y, H:i")
                                ->withoutSeconds()
                                ->default(fn(): string => now()),

                            Components\Toggle::make('visibility')
                                ->label(__('Visibility'))
                                ->default(false),
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
                                    $set('currency', Currencies::getCodeByCountry($get('country')));
                                })
//                                ->default("armenia")
                                ->nullable(),

                            Components\Select::make('location')
                                ->label(__('Location'))
                                ->placeholder("-")
                                ->options(fn(Closure $get): array => Locations::asSelectArray($get('country')))
                                ->nullable(),
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
        $data['description'] = strReplace("<br><br><br>", "<br><br>", $data['description']);

        return $data;
    }

    /**
     * @param array $data
     * @return Model
     */
    protected function handleRecordCreation(array $data): Model
    {
        $record = $this->getModel()::create($data);

        $record->forceFill([
            'source' => ListingItemSource::ADMIN,
        ])->save();

        $seo = $data['seo'];

        // Replace and strip tags and entities.
        $defaultDescription = str($data['description'])->replace(["<br>", "</p><p>", "&nbsp;"], [" ", " ", " "]);
        $defaultDescription = strip_tags($defaultDescription);

        $record->seo->update([
            'title' => $seo['title'] ?: $data['title'],
            'description' => $seo['description'] ?: str($defaultDescription)->trim()->value(),
        ]);

        return $record;
    }
}

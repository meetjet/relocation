<?php

namespace App\Filament\Resources\ListingItemResource\Pages;

use App\Enums\ListingItemStatus;
use App\Facades\Locations;
use App\Facades\Countries;
use App\Facades\Currencies;
use App\Filament\Actions\Pages\DeleteAction;
use App\Filament\Resources\ListingItemResource;
use App\Models\ListingItem;
use App\Traits\PageListHelpers;
use Closure;
use Filament\Forms\Components;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EditListingItem extends EditRecord
{
    use PageListHelpers;

    protected static string $resource = ListingItemResource::class;

    /**
     * @return array
     * @throws \Exception
     */
    protected function getActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * @return array
     */
    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->context('edit')
                ->model($this->getRecord())
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
                                ->required(),

                            Components\Placeholder::make('original')
                                ->label(__('Original text'))
                                ->hidden(fn($record): bool => is_null($record) || is_null($record->original))
                                ->content(fn($record): ?string => $record->original),

                            Components\RichEditor::make('description')
                                ->label(__('Description'))
                                ->disableToolbarButtons([
                                    'attachFiles',
                                    'codeBlock',
                                ])
                                ->afterStateHydrated(function ($component, $state, $record) {
                                    if (is_null($state) && $record->status === ListingItemStatus::CREATED) {
                                        $component->state($record->original);
                                    }
                                })
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
                                        ->default(Currencies::getCodeByCountry("armenia"))
                                        ->disabled(),
                                ]),
                        ]),

                    Components\Section::make(__('Announcement owner'))
                        ->schema([
                            Components\TextInput::make('contact.nickname')
                                ->label(__('Real owner nickname'))
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

                            Components\Placeholder::make('user')
                                ->label(__('User'))
                                ->content(fn($record) => static::link(route('filament.resources.users.edit', $record->user), $record->user->name)),
                        ])->columns()->collapsible(),

                    Components\Section::make(__('SEO'))
                        ->schema([
                            Components\TextInput::make('seo.title')
                                ->label(__('SEO title'))
                                ->nullable(),

                            Components\Textarea::make('seo.description')
                                ->label(__('SEO description'))
                                ->rows(2)
                                ->nullable(),

                            Components\TextInput::make('seo.robots')
                                ->label(__('SEO robots'))
                                ->nullable(),
                        ])->collapsible(),
                ])
                ->columnSpan(['lg' => 2]),

            Components\Group::make()
                ->schema([
                    Components\Card::make()
                        ->schema([
                            Components\Placeholder::make('created_at')
                                ->label(__('Created at'))
                                ->content(fn($record): string => $record->created_at->diffForHumans()),

                            Components\Placeholder::make('updated_at')
                                ->label(__('Last modified at'))
                                ->content(fn($record): string => $record->updated_at->diffForHumans()),
                        ]),

                    Components\Card::make()
                        ->schema([
                            Components\Select::make('status')
                                ->label(__('Status'))
                                ->options(ListingItemStatus::asSelectArray())
                                ->disablePlaceholderSelection()
                                ->reactive()
                                ->afterStateUpdated(function (ListingItem $record, Closure $set, Closure $get) {
                                    if (is_null($record->published_at)) {
                                        if ($get('status') === ListingItemStatus::PUBLISHED) {
                                            $set('published_at', now());
                                        } else {
                                            $set('published_at', null);
                                        }
                                    }
                                })
                                ->required(),

                            Components\DateTimePicker::make('published_at')
                                ->label(__('Published at'))
                                ->displayFormat("j M Y, H:i")
                                ->withoutSeconds(),

                            Components\Toggle::make('visibility')
                                ->label(__('Visibility')),
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
                                ->disablePlaceholderSelection()
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
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['seo'] = $this->record->seo->toArray();

        return $data;
    }

    /**
     * @param Model $record
     * @param array $data
     * @return Model
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->seo->update($data['seo']);

        return parent::handleRecordUpdate($record, $data);
    }

    /**
     * @return string
     */
    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? self::getResource()::getUrl('index');
    }
}

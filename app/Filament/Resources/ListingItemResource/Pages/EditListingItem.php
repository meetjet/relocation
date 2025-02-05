<?php

namespace App\Filament\Resources\ListingItemResource\Pages;

use App\Enums\ListingItemSource;
use App\Enums\ListingItemStatus;
use App\Facades\Locations;
use App\Facades\Countries;
use App\Facades\Currencies;
use App\Filament\Actions\Pages\DeleteAction;
use App\Filament\Resources\ListingItemResource;
use App\Jobs\TelegramNotifyAnnouncementPublishedJob;
use App\Jobs\TelegramNotifyAnnouncementRejectedJob;
use App\Jobs\TelegramSendAnnouncementToChannelJob;
use App\Models\ListingItem;
use App\Traits\PageListHelpers;
use Closure;
use Exception;
use Filament\Forms\Components;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EditListingItem extends EditRecord
{
    use PageListHelpers;

    protected static string $resource = ListingItemResource::class;

    /**
     * @return array
     * @throws Exception
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
                            Components\ViewField::make('frontend_url')
                                ->view('forms.components.view-on-frontend-button')
                                ->disableLabel()
                                ->hidden(fn($record): bool => is_null($record->frontend_url)),

                            Components\Placeholder::make('source')
                                ->label(__('Source'))
                                ->content(fn($record): string => ListingItemSource::getDescription($record->source))
                                ->hidden(fn($record): bool => is_null($record->source)),

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
     * @param array $data
     * @return array
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Remove redundant line breaks.
        if ($data['description']) {
            $data['description'] = strReplace("<br><br><br>", "<br><br>", $data['description']);
        }

        return $data;
    }

    /**
     * @param Model $record
     * @param array $data
     * @return Model
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $seo = $record->seo;
        $seo->updateOrCreate($seo->toArray(), $data['seo']);

        return parent::handleRecordUpdate($record, $data);
    }

    /**
     * @return string
     */
    protected function getRedirectUrl(): string
    {
        $previousUrl = $this->getPreviousUrl();

        return $previousUrl ?? self::getResource()::getUrl('index');
    }

    /**
     * @param \Filament\Pages\Actions\DeleteAction $action
     */
    protected function configureDeleteAction(\Filament\Pages\Actions\DeleteAction $action): void
    {
        $resource = static::getResource();
        $previousUrl = $this->getPreviousUrl(true);

        $action
            ->authorize($resource::canDelete($this->getRecord()))
            ->record($this->getRecord())
            ->recordTitle($this->getRecordTitle())
            ->successRedirectUrl($previousUrl ?? $resource::getUrl('index'));
    }

    /**
     * @return Action
     * @throws Exception
     */
    protected function getCancelFormAction(): Action
    {
        $previousUrl = $this->getPreviousUrl();

        return Action::make('cancel')
            ->label(__('filament::resources/pages/edit-record.form.actions.cancel.label'))
            ->url($previousUrl ?? static::getResource()::getUrl())
            ->color('secondary');
    }

    /**
     * @param bool $sectionOnly
     * @return string|null
     */
    private function getPreviousUrl(bool $sectionOnly = false): ?string
    {
        if ($this->previousUrl) {

            if (isUrlWithCountry($this->previousUrl)) {
                return $sectionOnly
                    ? addSubdomainToUrl(route('listings.index'), $this->record->country)
                    : addSubdomainToUrl(route('listings.show', [$this->record->category->slug, $this->record->uuid]), $this->record->country);
            }

            return $this->previousUrl;
        }

        return null;
    }

    protected function afterSave(): void
    {
        if (
            $this->record->status === ListingItemStatus::PUBLISHED
            && $this->record->visibility === true
            && $this->record->country
            && $this->record->category
            && $this->record->uuid
            && $this->record->telegram_chat_id
            && is_null($this->record->telegram_published_notify_sent)
        ) {
            TelegramNotifyAnnouncementPublishedJob::dispatch($this->record);
        }

        if (
            $this->record->status === ListingItemStatus::PUBLISHED
            && $this->record->visibility === true
            && $this->record->country
            && $this->record->category
            && $this->record->uuid
            && is_null($this->record->telegram_to_channel_sent)
        ) {
            TelegramSendAnnouncementToChannelJob::dispatch($this->record);
        }

        if (
            $this->record->status === ListingItemStatus::REJECTED
            && $this->record->country
            && $this->record->telegram_chat_id
            && is_null($this->record->telegram_rejected_notify_sent)
        ) {
            TelegramNotifyAnnouncementRejectedJob::dispatch($this->record);
        }
    }
}

<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Enums\EventPaymentType;
use App\Enums\EventStatus;
use App\Facades\Currencies;
use App\Facades\Locations;
use App\Facades\Countries;
use App\Filament\Actions\Pages\DeleteAction;
use App\Filament\Resources\EventResource;
use App\Forms\Components\ResendEventToTelegramChannel;
use App\Forms\Components\RichEditor;
use App\Jobs\TelegramSendEventToChannelJob;
use App\Models\Event;
use App\Models\Picture;
use App\Traits\PageListHelpers;
use App\UploadIO\UploadIO;
use Closure;
use Exception;
use Filament\Forms\Components;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class EditEvent extends EditRecord
{
    use PageListHelpers;

    protected static string $resource = EventResource::class;

    /**
     * @return array
     * @throws Exception
     */
    protected function getActions(): array
    {
        return [
            Action::make('cloneEvent')
                ->label(__('Copy event'))
                ->icon('heroicon-s-duplicate')
                ->requiresConfirmation()
                ->action('cloneEvent'),
            DeleteAction::make(),
        ];
    }

    public function cloneEvent(): void
    {
        $action = $this->getMountedAction();

        if (is_null($action)) {
            Log::error("Copying event: failed to get action.");
            return;
        }

        try {
            DB::transaction(function () use ($action) {
                $sourceEvent = $this->record;

                $newEvent = $sourceEvent->replicate([
                    'uuid',
                    'status',
                    'visibility',
                    'deleted_at',
                    'published_at',
                    'telegram_chat_id',
                    'telegram_message_id',
                    'telegram_to_channel_sent',
                ]);
                $newEvent->title .= ' (' . __('copy') . ')';
                $newEvent->save();

                $newEvent->attachTags($sourceEvent->tagsWithType("events"));

                $sourceEvent->pictures->each(function ($_picture) use ($newEvent) {
                    $newEvent->pictures()->create([
                        'caption' => $_picture->caption,
                        'tmp_image' => $this->makeTmpImage($_picture),
                    ]);
                });

                $action->successRedirectUrl(route("filament.resources.events.edit", $newEvent));
                $action->successNotificationTitle(__('Event copied successfully'));
                $action->success();
                Log::info("Copying event: successful.");
            });
        } catch (Exception $e) {
            $action->failureNotificationTitle(__('Failed to copy event'));
            $action->failure();
            Log::error('Copying event: ' . $e->getMessage());
        }
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
                                        ->afterStateUpdated(fn(Closure $set, Closure $get) => $set('currency', Currencies::getCodeByCountry($get('country'))))
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
                        ]),

                    Components\Section::make(__('Event owner'))
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
//                                ->helperText(__('Requested from the user if he does not have a nickname'))
                                ->email()
                                ->requiredWithoutAll(['contact.nickname', 'custom_nickname', 'phone']),

                            Components\TextInput::make('phone')
                                ->label(__('Owner phone'))
//                                ->helperText(__('Requested from the user if he does not have a nickname'))
                                ->requiredWithoutAll(['contact.nickname', 'custom_nickname', 'email']),

                            Components\Placeholder::make('user')
                                ->label(__('User'))
                                ->content(fn($record) => static::link(route('filament.resources.users.edit', $record->user), $record->user->name)),
                        ])->columns(),

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
                        ])->collapsed(),
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
                                ->options(EventStatus::asSelectArray())
                                ->disablePlaceholderSelection()
                                ->reactive()
                                ->afterStateUpdated(function (Event $record, Closure $set, Closure $get) {
                                    if (is_null($record->published_at)) {
                                        if ($get('status') === EventStatus::PUBLISHED) {
                                            $set('published_at', now());
                                        } else {
                                            $set('published_at', null);
                                        }
                                    }
                                }),

                            Components\DateTimePicker::make('published_at')
                                ->label(__('Published at'))
                                ->displayFormat("j M Y, H:i")
                                ->withoutSeconds(),

                            Components\Toggle::make('visibility')
                                ->label(__('Visibility'))
                                ->reactive(),
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

                    Components\Section::make(__('Reposting to a channel'))
                        ->hidden(fn($record, Closure $get) => !(!is_null($record->telegram_message_id)
                            && $get('status') === EventStatus::PUBLISHED
                            && $get('visibility') === true))
                        ->schema([
                            ResendEventToTelegramChannel::make('resend_event_manager')
                                ->disableLabel(),
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

        // Default data to store the actual data in the database (as a point of reference).
        $defaultData = [
            'price' => null,
            'currency' => null,
        ];

        return array_merge($defaultData, $data);
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

    protected function afterSave(): void
    {
        logger("Event update on event with id = {$this->record->id}");

        if (
            $this->record->status === EventStatus::PUBLISHED
            && $this->record->visibility === true
            && $this->record->country
            && $this->record->category
            && $this->record->uuid
            && is_null($this->record->telegram_to_channel_sent)
        ) {
            TelegramSendEventToChannelJob::dispatch($this->record);
        }
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
                    ? addSubdomainToUrl(route('events.index'), $this->record->country)
                    : addSubdomainToUrl(route('events.show', [$this->record->category->slug, $this->record->uuid]), $this->record->country);
            }

            return $this->previousUrl;
        }

        return null;
    }

    /**
     * @param Picture $picture
     * @return string
     * @throws RequestException
     */
    private function makeTmpImage(Picture $picture): string
    {
        if ($picture->uploadio_file_path) {
            $tmpImagePath = 'form-attachments-tmp/' . Str::random(40);

            Storage::put(
                $tmpImagePath,
                app(UploadIO::class)->download($picture->uploadio_file_path)
            );

            return $tmpImagePath;
        }

        if ($picture->local_file_path) {
            return Storage::putFile(
                'form-attachments-tmp',
                new File(storage_path("app/public/{$picture->local_file_path}"))
            );
        }

        throw new RuntimeException('Failed to create temporary image file to copy');
    }
}

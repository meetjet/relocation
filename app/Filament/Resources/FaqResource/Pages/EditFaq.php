<?php

namespace App\Filament\Resources\FaqResource\Pages;

use App\Enums\FaqStatus;
use App\Facades\Countries;
use App\Filament\Resources\FaqResource;
use Exception;
use Filament\Pages\Actions;
use Filament\Forms\Components;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EditFaq extends EditRecord
{
    protected static string $resource = FaqResource::class;

    /**
     * @return array
     * @throws Exception
     */
    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
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
                            Components\Placeholder::make('original')
                                ->label(__('Original text'))
                                ->hidden(fn($record): bool => is_null($record) || is_null($record->original))
                                ->content(fn($record): ?string => $record->original),

                            Components\Textarea::make('title')
                                ->label(__('Title'))
                                ->rows(2)
                                ->required()
                                ->autofocus(fn($record): bool => is_null($record->title) && $record->status === FaqStatus::CREATED),

                            Components\TextInput::make('slug')
                                ->label(__('Slug'))
                                ->hint(__('If this field is left blank, the link will be generated automatically'))
                                ->unique(ignoreRecord: true),

                            Components\RichEditor::make('question')
                                ->label(__('Question'))
                                ->disableToolbarButtons([
                                    'attachFiles',
                                    'codeBlock',
                                ])
                                ->required(),

                            Components\RichEditor::make('answer')
                                ->label(__('Answer'))
                                ->disableToolbarButtons([
                                    'attachFiles',
                                    'codeBlock',
                                ])
                                ->required(),

                            Components\SpatieTagsInput::make('tags')
                                ->label(__('Tags'))
                                ->type("faqs"),
                        ]),

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
                        ]),
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
                            Components\Select::make('country')
                                ->label(__('Country'))
                                ->options(Countries::asSelectArray())
                                ->placeholder("-")
                                ->nullable(),

                            Components\Select::make('status')
                                ->label(__('Status'))
                                ->options(FaqStatus::asSelectArray())
                                ->disablePlaceholderSelection(),

                            Components\Toggle::make('visibility')
                                ->label(__('Visibility')),
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
        $data['slug'] = Str::startsWith($data['slug'], "new-faq-")
            ? null
            : $data['slug'];

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
        $seo = $record->seo;
        $seo->updateOrCreate($seo->toArray(), $data['seo']);

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

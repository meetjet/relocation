<?php

namespace App\Filament\Resources\FaqResource\Pages;

use App\Enums\FaqStatus;
use App\Facades\Countries;
use App\Filament\Resources\FaqResource;
use Filament\Forms\Components;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

/**
 * @package App\Filament\Resources\FaqResource\Pages
 */
class CreateFaq extends CreateRecord
{
    protected static string $resource = FaqResource::class;

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
                            Components\Placeholder::make('original')
                                ->label(__('Original text'))
                                ->hidden(fn($record): bool => is_null($record) || is_null($record->original))
                                ->content(fn($record): ?string => $record->original),

                            Components\Textarea::make('title')
                                ->label(__('Title'))
                                ->rows(2)
                                ->required()
                                ->autofocus(),

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

                            Components\Hidden::make('user_id')
                                ->default(fn(): int => Request::user()->id),
                        ]),

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
                            Components\Select::make('country')
                                ->label(__('Country'))
                                ->options(Countries::asSelectArray())
                                ->placeholder("-")
                                ->nullable(),

                            Components\Select::make('status')
                                ->label(__('Status'))
                                ->options(FaqStatus::asSelectArray())
                                ->disablePlaceholderSelection()
                                ->default(FaqStatus::CREATED),

                            Components\Toggle::make('visibility')
                                ->label(__('Visibility'))
                                ->default(false),
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
        if ($data['question']) {
            $data['question'] = strReplace("<br><br><br>", "<br><br>", $data['question']);
        }

        if ($data['answer']) {
            $data['answer'] = strReplace("<br><br><br>", "<br><br>", $data['answer']);
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

        // Replace and strip tags and entities.
        $defaultQuestion = str($data['question'])->replace(["<br>", "</p><p>", "&nbsp;"], [" ", " ", " "]);
        $defaultQuestion = strip_tags($defaultQuestion);

        $record->seo->update([
            'title' => $seo['title'] ?: $data['title'],
            'description' => $seo['description'] ?: str($defaultQuestion)->trim()->value(),
        ]);

        return $record;
    }

    /**
     * @return string
     */
    protected function getRedirectUrl(): string
    {
        return self::getResource()::getUrl('index');
    }
}

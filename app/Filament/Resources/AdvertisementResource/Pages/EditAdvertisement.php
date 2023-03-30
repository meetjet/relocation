<?php

namespace App\Filament\Resources\AdvertisementResource\Pages;

use App\Enums\AdvertisementStatus;
use App\Facades\Countries;
use App\Filament\Actions\Pages\DeleteAction;
use App\Filament\Resources\AdvertisementResource;
use Exception;
use Filament\Forms\Components;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditAdvertisement extends EditRecord
{
    protected static string $resource = AdvertisementResource::class;

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

                            Components\RichEditor::make('description')
                                ->label(__('Description'))
                                ->disableToolbarButtons([
                                    'attachFiles',
                                    'codeBlock',
                                ])
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
                            Components\Select::make('status')
                                ->label(__('Status'))
                                ->options(AdvertisementStatus::asSelectArray())
                                ->disablePlaceholderSelection(),

                            Components\Toggle::make('visibility')
                                ->label(__('Visibility')),
                        ]),

                    Components\Card::make()
                        ->schema([
                            Components\Select::make('country')
                                ->label(__('Country'))
                                ->options(Countries::asSelectArray())
                                ->placeholder("-")
                                ->nullable(),
                        ]),
                ])
                ->columnSpan(['lg' => 1]),
        ];
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('Save'))
                ->action('saveAction')
                ->keyBindings(['mod+s']),
            Action::make('saveAndClose')
                ->label(__('Save & close'))
                ->action('saveAndCloseAction')
                ->keyBindings(['mod+shift+s'])
                ->color('secondary'),
            $this->getCancelFormAction(),
        ];
    }

    public function saveAction(): void
    {
        $this->save(false);
    }

    public function saveAndCloseAction(): void
    {
        $this->redirect($this->previousUrl ?? self::getResource()::getUrl('index'));
        $this->save();
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
}

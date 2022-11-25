<?php

namespace App\Filament\Resources;

use App\Enums\EventPointStatus;
use App\Filament\Resources\EventPointResource\Pages;
use App\Models\EventPoint;
use App\Traits\PageListHelpers;
use Exception;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use App\Filament\Actions;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EventPointResource extends Resource
{
    use PageListHelpers;

    protected static ?string $model = EventPoint::class;

    protected static ?string $slug = 'events/points';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?int $navigationSort = 2;

    /**
     * @return string
     */
    public static function getNavigationGroup(): string
    {
        return __('Events');
    }

    /**
     * @return string
     */
    public static function getNavigationLabel(): string
    {
        return __('Points');
    }

    /**
     * @return string
     */
    public static function getModelLabel(): string
    {
        return __('Point');
    }

    /**
     * @return string
     */
    public static function getPluralModelLabel(): string
    {
        return __('Point');
    }

    /**
     * @param Form $form
     * @return Form
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label(__('Title'))
                            ->required(),

                        Forms\Components\TextInput::make('slug')
                            ->label(__('Slug'))
                            ->hint(__('If this field is left blank, the link will be generated automatically'))
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('address')
                            ->label(__('Address'))
                            ->required(),

                        Forms\Components\RichEditor::make('description')
                            ->label(__('Description'))
                            ->disableToolbarButtons([
                                'attachFiles',
                                'codeBlock',
                            ])
                            ->nullable(),

                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label(__('Status'))
                                    ->options(EventPointStatus::asSelectArray())
                                    ->placeholder("-")
                                    ->default(EventPointStatus::ACTIVE)
                                    ->required(),

                                Forms\Components\Toggle::make('visibility')
                                    ->label(__('Visibility'))
                                    ->default(false),
                            ])
                            ->columns(),
                    ]),
            ])
            ->columns(1);
    }

    /**
     * @param Table $table
     * @return Table
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable()
                    ->sortable()
                    ->color(fn($record): ?string => is_null($record->deleted_at) ? null : "danger"),

                Tables\Columns\TextColumn::make('address')
                    ->label(__('Address'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('events')
                    ->label(__('Events'))
                    ->getStateUsing(fn(EventPoint $record): ?string => $record->events()->count())
                    ->toggleable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label(__('Status'))
                    ->enum(EventPointStatus::asSelectArray())
                    ->sortable()
                    ->colors([
                        'secondary' => EventPointStatus::INACTIVE,
                        'success' => EventPointStatus::ACTIVE,
                    ])
                    ->toggleable(),

                Tables\Columns\IconColumn::make('visibility')
                    ->label(__('Visibility'))
                    ->boolean()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters(self::getFilters())
            ->actions([
                Actions\Tables\EditAction::make()
                    ->label("")
                    ->tooltip(__('Edit')),
                Actions\Tables\DeleteAction::make()
                    ->label("")
                    ->tooltip(__('Delete'))
                    ->visible(fn(EventPoint $record): bool => !$record->events()->exists()),
                Actions\Tables\RestoreAction::make()
                    ->label("")
                    ->tooltip(__('Restore')),
            ])
            ->bulkActions([])
            ->defaultSort('title');
    }

    /**
     * @return array
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageEventPoints::route('/'),
        ];
    }

    /**
     * @return Builder
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    /**
     * @return array
     * @throws Exception
     */
    public static function getFilters(): array
    {
        return [
            Tables\Filters\TrashedFilter::make(),

            Tables\Filters\Filter::make('status')
                ->form([
                    Forms\Components\Select::make('status')
                        ->label(__('Status'))
                        ->placeholder("-")
                        ->options(EventPointStatus::asSelectArray()),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query->when(
                        $data['status'],
                        fn(Builder $query, $status): Builder => $query->where('status', $status),
                    );
                })
                ->indicateUsing(function (array $data): ?string {
                    if ($data['status']) {
                        return __('Status') . ' "' . EventPointStatus::getDescription($data['status']) . '"';
                    }

                    return null;
                }),

            Tables\Filters\Filter::make('visibility')
                ->form([
                    Forms\Components\Select::make('visibility')
                        ->label(__('Visibility'))
                        ->placeholder("-")
                        ->options([
                            'true' => __("Yes"),
                            'false' => __("No"),
                        ]),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query->when(
                        $data['visibility'],
                        fn(Builder $query, $visibility): Builder => $query->where('visibility', json_decode($visibility)),
                    );
                })
                ->indicateUsing(function (array $data): ?string {
                    if ($data['visibility']) {
                        return __('Visibility') . ' "' . (json_decode($data['visibility']) ? __("Yes") : __("No")) . '"';
                    }

                    return null;
                }),
        ];
    }
}

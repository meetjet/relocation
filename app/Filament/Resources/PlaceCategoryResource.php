<?php

namespace App\Filament\Resources;

use App\Enums\CategoryStatus;
use App\Filament\Resources\PlaceCategoryResource\Pages\ManagePlaceCategories;
use App\Models\PlaceCategory;
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

class PlaceCategoryResource extends Resource
{
    use PageListHelpers;

    protected static ?string $model = PlaceCategory::class;

    protected static ?string $slug = 'places/categories';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?int $navigationSort = 1;

    /**
     * @return string
     */
    public static function getNavigationGroup(): string
    {
        return __('Places');
    }

    /**
     * @return string
     */
    public static function getNavigationLabel(): string
    {
        return __('Categories');
    }

    /**
     * @return string
     */
    public static function getModelLabel(): string
    {
        return __('Category');
    }

    /**
     * @return string
     */
    public static function getPluralModelLabel(): string
    {
        return __('Categories');
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
                                    ->options(CategoryStatus::asSelectArray())
                                    ->placeholder("-")
                                    ->default(CategoryStatus::ACTIVE)
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

                Tables\Columns\TextColumn::make('places')
                    ->label(__('Places'))
                    ->getStateUsing(fn(PlaceCategory $record): ?string => $record->places()->count()),

                Tables\Columns\BadgeColumn::make('status')
                    ->label(__('Status'))
                    ->enum(CategoryStatus::asSelectArray())
                    ->sortable()
                    ->colors([
                        'secondary' => CategoryStatus::INACTIVE,
                        'success' => CategoryStatus::ACTIVE,
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
                    ->visible(fn(PlaceCategory $record): bool => !$record->places()->exists()),
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
            'index' => ManagePlaceCategories::route('/'),
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
                        ->options(CategoryStatus::asSelectArray()),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query->when(
                        $data['status'],
                        fn(Builder $query, $status): Builder => $query->where('status', $status),
                    );
                })
                ->indicateUsing(function (array $data): ?string {
                    if ($data['status']) {
                        return __('Status') . ' "' . CategoryStatus::getDescription($data['status']) . '"';
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

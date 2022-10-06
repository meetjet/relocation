<?php

namespace App\Filament\Resources;

use App\Enums\FaqStatus;
use App\Filament\Resources\FaqResource\Pages;
use App\Filament\Resources\FaqResource\RelationManagers;
use App\Models\Faq;
use Closure;
use Exception;
use Filament\Forms\Components;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Actions;
use Filament\Tables\Columns;
use Filament\Tables\Filters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?int $navigationSort = 2;

    /**
     * @return string
     */
    public static function getNavigationLabel(): string
    {
        return __('FAQ');
    }

    /**
     * @return string
     */
    public static function getModelLabel(): string
    {
        return __('FAQ');
    }

    /**
     * @return string
     */
    public static function getPluralModelLabel(): string
    {
        return __('FAQ');
    }

    /**
     * @param Form $form
     * @return Form
     */
    public static function form(Form $form): Form
    {
//        return $form // TODO: for editing on page
//            ->schema([
//                Components\Card::make()
//                    ->schema(static::getMainSchema())
//                    ->columnSpan(['lg' => 3]),
//                Components\Card::make()
//                    ->schema(static::getInfoSchema())
//                    ->columnSpan(['lg' => 1]),
//            ])
//            ->columns(4);
        return $form // TODO: for edition in modal
        ->schema(static::getMainSchema())
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
                Columns\TextColumn::make('original')
                    ->getStateUsing(fn($record): string => $record->title ?: $record->original)
                    ->label(__('Question'))
                    ->wrap()
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->where('original', 'ilike', "%{$search}%")
                            ->orWhere('title', 'ilike', "%{$search}%")
                            ->orWhere('question', 'ilike', "%{$search}%");
                    })
                    ->color(fn($record): ?string => is_null($record->deleted_at) ? null : "danger")
                    ->sortable(),

                Columns\TextColumn::make('created_at')
                    ->label(__('Created'))
                    ->date()
                    ->sortable()
                    ->toggleable(),

                Columns\BadgeColumn::make('status')
                    ->label(__('Status'))
                    ->enum(FaqStatus::asSelectArray())
                    ->sortable()
                    ->colors([
                        'secondary' => FaqStatus::CREATED,
                        'success' => FaqStatus::PUBLISHED,
                        'danger' => FaqStatus::REJECTED,
                    ])
                    ->toggleable(),

                Columns\BooleanColumn::make('visibility')
                    ->label(__('Visibility'))
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters(static::getFilters())
            ->actions([
                Actions\EditAction::make(),
//                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make(),
                Actions\RestoreBulkAction::make(),
                Actions\ForceDeleteBulkAction::make(),
            ])
            ->defaultSort('id', 'desc');
    }

    /**
     * @return array
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFaqs::route('/'),
//            'create' => Pages\CreateFaq::route('/create'),
//            'edit' => Pages\EditFaq::route('/{record}/edit'), // TODO: deprecated
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
     */
    public static function getMainSchema(): array
    {
        $publishedStatus = FaqStatus::PUBLISHED;
        $statusOptions = Arr::only(FaqStatus::asSelectArray(), [
            FaqStatus::PUBLISHED,
            FaqStatus::REJECTED,
        ]);

        return [
            // Question editing form.
            Components\Group::make()
                ->schema([
                    Components\Placeholder::make('original')
                        ->label(__('Original text'))
                        ->content(fn(Faq $record): string => $record['original']),

                    Components\Textarea::make('title')
                        ->label(__('Title'))
                        ->rows(2),

                    Components\MarkdownEditor::make('question')
                        ->label(__('Question'))
                        ->rules(['nullable', "required_if:status,{$publishedStatus}"]),

                    Components\MarkdownEditor::make('answer')
                        ->label(__('Answer'))
                        ->requiredWith('question'),

                    Components\Group::make()
                        ->schema([
                            Components\Select::make('status')
                                ->label(__('Status'))
                                ->options($statusOptions)
                                ->required(),
                            Components\Toggle::make('visibility')
                                ->label(__('Visibility'))
                                ->default(true),
                        ])
                        ->columns(),
                ])
                ->hidden(fn($record): bool => is_null($record)),

            // Form for creating a new question.
            Components\Group::make()
                ->schema([
                    Components\MarkdownEditor::make('original')
                        ->label(__('Original text'))
                        ->required(),
                ])
                ->hidden(fn($record): bool => !is_null($record))
        ];
    }

    /**
     * @return array
     */
    public static function getInfoSchema(): array
    {
        return [
            Components\Placeholder::make('created_at')
                ->label(__('Created at'))
                ->content(fn(Faq $record): string => $record->created_at->diffForHumans()),

            Components\Placeholder::make('updated_at')
                ->label(__('Last modified at'))
                ->content(fn(Faq $record): string => $record->updated_at->diffForHumans()),
        ];
    }

    public static function getFilters(): array
    {
        return [
            Filters\TrashedFilter::make(),

            Filters\Filter::make('status')
                ->form([
                    Components\Select::make('status')
                        ->label(__('Status'))
                        ->placeholder("-")
                        ->options(FaqStatus::asSelectArray()),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query->when(
                        $data['status'],
                        fn(Builder $query, $status): Builder => $query->where('status', $status),
                    );
                })
                ->indicateUsing(function (array $data): ?string {
                    if ($data['status']) {
                        return __('Status') . ' "' . FaqStatus::getDescription($data['status']) . '"';
                    }

                    return null;
                }),

            Filters\Filter::make('visibility')
                ->form([
                    Components\Select::make('visibility')
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

            Filters\Filter::make('created_at')
                ->form([
                    Components\DatePicker::make('published_from')
                        ->label(__('Created from'))
                        ->maxDate(Carbon::today())
                        ->placeholder(fn($state): string => Carbon::parse(now())
                            ->setTimezone(config('app.timezone'))
                            ->translatedFormat(config('tables.date_format'))),
                    Components\DatePicker::make('published_until')
                        ->label(__('Created until'))
                        ->maxDate(Carbon::today())
                        ->placeholder(fn($state): string => Carbon::parse(now())
                            ->setTimezone(config('app.timezone'))
                            ->translatedFormat(config('tables.date_format'))),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['published_from'],
                            fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                        )
                        ->when(
                            $data['published_until'],
                            fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                        );
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];
                    if ($data['published_from'] ?? null) {
                        $indicators['published_from'] = __('Created from') . ' ' . Carbon::parse($data['published_from'])
                                ->setTimezone(config('app.timezone'))
                                ->translatedFormat(config('tables.date_format'));
                    }
                    if ($data['published_until'] ?? null) {
                        $indicators['published_until'] = __('Created until') . ' ' . Carbon::parse($data['published_until'])
                                ->setTimezone(config('app.timezone'))
                                ->translatedFormat(config('tables.date_format'));
                    }

                    return $indicators;
                }),
        ];
    }
}

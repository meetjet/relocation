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
                    ->getStateUsing(fn($record): string => $record->question ?: $record->original)
                    ->label(__('Question'))
                    ->wrap()
                    ->limit(150)
                    ->searchable()
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
            'create' => Pages\CreateFaq::route('/create'),
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
            Components\Placeholder::make('original')
                ->label(__('Original text'))
                ->content(fn(Faq $record): string => $record['original']),

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
                        ->label(__('Visibility')),
                ])
                ->columns(),
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

            Filters\Filter::make('created_at')
                ->form([
                    Components\DatePicker::make('published_from')
                        ->label(__('Created from'))
                        ->placeholder(fn ($state): string => now()->format('M d, Y')),
                    Components\DatePicker::make('published_until')
                        ->label(__('Created until'))
                        ->placeholder(fn ($state): string => now()->format('M d, Y')),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['published_from'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                        )
                        ->when(
                            $data['published_until'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                        );
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];
                    if ($data['published_from'] ?? null) {
                        $indicators['published_from'] = __('Created from') . ' ' . Carbon::parse($data['published_from'])->toFormattedDateString();
                    }
                    if ($data['published_until'] ?? null) {
                        $indicators['published_until'] = __('Created until') . ' ' . Carbon::parse($data['published_until'])->toFormattedDateString();
                    }

                    return $indicators;
                }),
        ];
    }
}

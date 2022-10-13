<?php

namespace App\Filament\Resources;

use App\Enums\FaqStatus;
use App\Filament\Resources\FaqResource\Pages;
use App\Filament\Resources\FaqResource\RelationManagers;
use App\Models\Faq;
use Exception;
use Filament\Forms\Components;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use App\Filament\Actions;
use Filament\Tables\Columns;
use Filament\Tables\Filters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Request;

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
        return $form
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
                    ->getStateUsing(fn($record): ?string => $record->title ?: $record->original)
                    ->label(__('Question'))
                    ->description(fn($record): ?string => $record->slug)
                    ->limit(200)
                    ->wrap()
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->where('original', 'ilike', "%{$search}%")
                            ->orWhere('title', 'ilike', "%{$search}%")
                            ->orWhere('question', 'ilike', "%{$search}%");
                    })
                    ->color(fn($record): ?string => is_null($record->deleted_at) ? null : "danger")
                    ->sortable(),

                Columns\SpatieTagsColumn::make('tags')
                    ->label(__('Tags'))
                    ->type("faqs")
                    ->toggleable(),

                Columns\TextColumn::make('created_at')
                    ->label(__('Created'))
                    ->date("j M Y")
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

                Columns\IconColumn::make('visibility')
                    ->label(__('Visibility'))
                    ->boolean()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters(static::getFilters())
            ->actions([
                Actions\Tables\EditAction::make()
                    ->label("")
                    ->tooltip(__('Edit')),
                Actions\Tables\DeleteAction::make()
                    ->label("")
                    ->tooltip(__('Delete')),
            ])
            ->bulkActions([
                Actions\Tables\DeleteBulkAction::make(),
                Actions\Tables\RestoreBulkAction::make(),
                Actions\Tables\ForceDeleteBulkAction::make(),
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
        return [
            Components\Group::make()
                ->schema([
                    Components\Placeholder::make('original')
                        ->label(__('Original text'))
                        ->hidden(fn($record): bool => is_null($record) || is_null($record->original))
                        ->content(fn($record): ?string => $record->original),

                    Components\Textarea::make('title')
                        ->label(__('Title'))
                        ->rows(2)
//                        ->rules(['nullable', 'required_if:status,' . FaqStatus::PUBLISHED]), // TODO: does not work
                        ->required(fn($record): bool => is_null($record)),

                    Components\TextInput::make('slug')
                        ->label(__('Slug'))
                        ->unique(ignoreRecord: true),

                    Components\RichEditor::make('question')
                        ->label(__('Question'))
                        ->disableToolbarButtons([
                            'attachFiles',
                            'codeBlock',
                        ])
                        ->required(fn($record): bool => is_null($record))
                        ->requiredWith(fn($record): ?string => is_null($record) ? null : "title"),

                    Components\RichEditor::make('answer')
                        ->label(__('Answer'))
                        ->disableToolbarButtons([
                            'attachFiles',
                            'codeBlock',
                        ])
                        ->required(fn($record): bool => is_null($record))
                        ->requiredWith(fn($record): ?string => is_null($record) ? null : "question"),

                    Components\SpatieTagsInput::make('tags')
                        ->label(__('Tags'))
                        ->type("faqs"),

                    Components\Group::make()
                        ->schema([
                            Components\Select::make('status')
                                ->label(__('Status'))
                                ->options(FaqStatus::asSelectArray())
                                ->placeholder("-")
                                ->required(),
                            Components\Toggle::make('visibility')
                                ->label(__('Visibility'))
                                ->default(false),
                        ])
                        ->columns(),

                    Components\Hidden::make('user_id')
                        ->default(fn(): int => Request::user()->id),
                ]),
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
                        ->displayFormat("j M Y")
                        ->maxDate(Carbon::today())
                        ->placeholder(fn($state): string => Carbon::parse(now())
                            ->setTimezone(config('app.timezone'))
                            ->translatedFormat("j M Y")),
                    Components\DatePicker::make('published_until')
                        ->label(__('Created until'))
                        ->displayFormat("j M Y")
                        ->maxDate(Carbon::today())
                        ->placeholder(fn($state): string => Carbon::parse(now())
                            ->setTimezone(config('app.timezone'))
                            ->translatedFormat("j M Y")),
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
                                ->translatedFormat("j M Y");
                    }
                    if ($data['published_until'] ?? null) {
                        $indicators['published_until'] = __('Created until') . ' ' . Carbon::parse($data['published_until'])
                                ->setTimezone(config('app.timezone'))
                                ->translatedFormat("j M Y");
                    }

                    return $indicators;
                }),
        ];
    }
}

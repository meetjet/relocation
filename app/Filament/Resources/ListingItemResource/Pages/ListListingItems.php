<?php

namespace App\Filament\Resources\ListingItemResource\Pages;

use App\Enums\ListingItemSource;
use App\Enums\ListingItemStatus;
use App\Facades\Locations;
use App\Facades\Countries;
use App\Filament\Resources\ListingItemResource;
use App\Models\ListingCategory;
use App\Traits\PageListHelpers;
use Closure;
use Exception;
use Filament\Forms\Components;
use Filament\Pages;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Table;
use App\Filament\Actions;
use Filament\Tables\Columns;
use Filament\Tables\Filters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ListListingItems extends ListRecords
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
            Pages\Actions\CreateAction::make(),
        ];
    }

    /**
     * @param Table $table
     * @return Table
     */
    public function table(Table $table): Table
    {
        return $table->defaultSort('created_at', 'desc');
    }

    /**
     * @return array
     */
    protected function getTableColumns(): array
    {
        return [
            Columns\TextColumn::make('title')
                ->label(__('Title'))
                ->description(fn($record) => $record->frontend_url
                    ? static::externalLink($record->frontend_url, Str::lower(__('Link')))
                    : null)
                ->limit(200)
                ->wrap()
                ->searchable()
                ->sortable()
                ->color(fn($record): ?string => is_null($record->deleted_at) ?: "danger"),

            Columns\TextColumn::make('source')
                ->label(__('Source'))
                ->enum(ListingItemSource::asSelectArray())
                ->color(fn($record): ?string => $record->source === ListingItemSource::BOT ? "success" : null)
                ->weight(fn($record): ?string => $record->source === ListingItemSource::BOT ? "bold" : null)
                ->toggleable(),

            Columns\SpatieTagsColumn::make('tags')
                ->label(__('Tags'))
                ->type("listing-items")
                ->toggleable(),

            Columns\TextColumn::make('price')
                ->label(__('Price'))
                ->toggleable(),

            Columns\TextColumn::make('country')
                ->label(__('Country'))
                ->enum(Countries::asSelectArray())
                ->sortable()
                ->toggleable(),

            Columns\TextColumn::make('location')
                ->label(__('Location'))
                ->getStateUsing(fn($record): string => Locations::getDescription($record->country, $record->location))
                ->toggleable(),

            Columns\TextColumn::make('category.title')
                ->label(__('Category'))
                ->wrap()
                ->sortable()
                ->toggleable(),

            Columns\TextColumn::make('created_at')
                ->label(__('Created at'))
                ->date("j M Y, H:i")
                ->sortable()
                ->toggleable(),

            Columns\TextColumn::make('published_at')
                ->label(__('Published at'))
                ->date("j M Y, H:i")
                ->sortable()
                ->toggleable(),

            Columns\BadgeColumn::make('status')
                ->label(__('Status'))
                ->enum(ListingItemStatus::asSelectArray())
                ->sortable()
                ->colors([
                    'secondary' => ListingItemStatus::CREATED,
                    'success' => ListingItemStatus::PUBLISHED,
                    'danger' => ListingItemStatus::REJECTED,
                ])
                ->toggleable(),

            Columns\IconColumn::make('visibility')
                ->label(__('Visibility'))
                ->boolean()
                ->sortable()
                ->toggleable(),
        ];
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function getTableActions(): array
    {
        return [
            Actions\Tables\EditAction::make()
                ->label("")
                ->tooltip(__('Edit')),
            Actions\Tables\DeleteAction::make()
                ->label("")
                ->tooltip(__('Delete')),
            Actions\Tables\RestoreAction::make()
                ->label("")
                ->tooltip(__('Restore')),
        ];
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function getTableBulkActions(): array
    {
        return [
            Actions\Tables\DeleteBulkAction::make(),
            Actions\Tables\RestoreBulkAction::make(),
            Actions\Tables\ForceDeleteBulkAction::make(),
        ];
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function getTableFilters(): array
    {
        return [
            Filters\TrashedFilter::make(),

            Filters\Filter::make('source')
                ->form([
                    Components\Select::make('source')
                        ->label(__('Source'))
                        ->placeholder("-")
                        ->options(ListingItemSource::asSelectArray()),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query->when(
                        $data['source'],
                        fn(Builder $query, $source): Builder => $query->where('data->source', $source),
                    );
                })
                ->indicateUsing(function (array $data): ?string {
                    if ($data['source']) {
                        return __('Source') . ' "' . ListingItemSource::getDescription($data['source']) . '"';
                    }

                    return null;
                }),

            Filters\Filter::make('country_and_location')
                ->form([
                    Components\Grid::make()
                        ->schema([
                            Components\Select::make('country')
                                ->label(__('Country'))
                                ->placeholder("-")
                                ->options(collect(Countries::asSelectArray())->put('no_country', __("No")))
                                ->reactive()
                                ->afterStateUpdated(fn(Closure $set, Closure $get) => $set('location', "")),

                            Components\Select::make('location')
                                ->label(__('Location'))
                                ->placeholder("-")
                                ->options(fn(Closure $get): Collection => collect(Locations::asSelectArray($get('country')))->put('no_location', __("No"))),
                        ])
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['country'],
                            fn(Builder $query, $country): Builder => $country === "no_country"
                                ? $query->whereNull('country')
                                : $query->where('country', $country),
                        )
                        ->when(
                            $data['location'],
                            fn(Builder $query, $location): Builder => $location === "no_location"
                                ? $query->whereNull('location')
                                : $query->where('location', $location),
                        );
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];

                    if ($data['country'] ?? null) {
                        $country = $data['country'] === "no_country"
                            ? __("No")
                            : Countries::getDescription($data['country']);
                        $indicators['country'] = __('Country') . ' "' . $country . '"';
                    }

                    if ($data['location'] ?? null) {
                        $location = $data['location'] === "no_location"
                            ? __("No")
                            : Locations::getDescription($data['country'], $data['location']);
                        $indicators['location'] = __('Location') . ' "' . $location . '"';
                    }

                    return $indicators;
                }),

            Filters\Filter::make('category')
                ->form([
                    Components\Select::make('category_id')
                        ->label(__('Category'))
                        ->placeholder("-")
                        ->options(ListingCategory::orderBy('title')->get()->pluck('title', 'id')->put('no_category', __("No"))),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query->when(
                        $data['category_id'],
                        fn(Builder $query, $category_id): Builder => $category_id === "no_category"
                            ? $query->whereNull('category_id')
                            : $query->where('category_id', $category_id),
                    );
                })
                ->indicateUsing(function (array $data): ?string {
                    if ($data['category_id']) {
                        $category = $data['category_id'] === "no_category"
                            ? __("No")
                            : ListingCategory::find($data['category_id'])->title;
                        return __('Category') . ' "' . $category . '"';
                    }

                    return null;
                }),

            Filters\Filter::make('status_and_visibility')
                ->form([
                    Components\Grid::make()
                        ->schema([
                            Components\Select::make('status')
                                ->label(__('Status'))
                                ->placeholder("-")
                                ->options(ListingItemStatus::asSelectArray()),

                            Components\Select::make('visibility')
                                ->label(__('Visibility'))
                                ->placeholder("-")
                                ->options([
                                    'true' => __("Yes"),
                                    'false' => __("No"),
                                ]),
                        ])
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['status'],
                            fn(Builder $query, $status): Builder => $query->where('status', $status),
                        )
                        ->when(
                            $data['visibility'],
                            fn(Builder $query, $visibility): Builder => $query->where('visibility', json_decode($visibility)),
                        );
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];

                    if ($data['status'] ?? null) {
                        $indicators['status'] = __('Status') . ' "' . ListingItemStatus::getDescription($data['status']) . '"';
                    }

                    if ($data['visibility'] ?? null) {
                        $indicators['visibility'] = __('Visibility') . ' "' . (json_decode($data['visibility']) ? __("Yes") : __("No")) . '"';
                    }

                    return $indicators;
                }),

            Filters\Filter::make('created_from_until')
                ->form([
                    Components\Grid::make()
                        ->schema([
                            Components\DatePicker::make('created_from')
                                ->label(__('Created from'))
                                ->displayFormat("j M Y"),

                            Components\DatePicker::make('created_until')
                                ->label(__('Created until'))
                                ->displayFormat("j M Y"),
                        ])
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['created_from'],
                            fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                        )
                        ->when(
                            $data['created_until'],
                            fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                        );
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];
                    if ($data['created_from'] ?? null) {
                        $indicators['created_from'] = __('Created from') . ' ' . Carbon::parse($data['created_from'])
                                ->translatedFormat("j M Y");
                    }
                    if ($data['created_until'] ?? null) {
                        $indicators['created_until'] = __('Created until') . ' ' . Carbon::parse($data['created_until'])
                                ->translatedFormat("j M Y");
                    }

                    return $indicators;
                }),

            Filters\Filter::make('published_from_until')
                ->form([
                    Components\Grid::make()
                        ->schema([
                            Components\DatePicker::make('published_from')
                                ->label(__('Published from'))
                                ->displayFormat("j M Y"),

                            Components\DatePicker::make('published_until')
                                ->label(__('Published until'))
                                ->displayFormat("j M Y"),
                        ])
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['published_from'],
                            fn(Builder $query, $date): Builder => $query->whereDate('published_at', '>=', $date),
                        )
                        ->when(
                            $data['published_until'],
                            fn(Builder $query, $date): Builder => $query->whereDate('published_at', '<=', $date),
                        );
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];
                    if ($data['published_from'] ?? null) {
                        $indicators['published_from'] = __('Published from') . ' ' . Carbon::parse($data['published_from'])
                                ->translatedFormat("j M Y");
                    }
                    if ($data['published_until'] ?? null) {
                        $indicators['published_until'] = __('Published until') . ' ' . Carbon::parse($data['published_until'])
                                ->translatedFormat("j M Y");
                    }

                    return $indicators;
                }),
        ];
    }

    /**
     * @return string
     */
    protected function getTableFiltersFormWidth(): string
    {
        return 'xl';
    }
}

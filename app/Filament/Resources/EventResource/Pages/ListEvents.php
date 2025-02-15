<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Enums\EventPaymentType;
use App\Enums\EventStatus;
use App\Facades\Locations;
use App\Facades\Countries;
use App\Filament\Resources\EventResource;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Place;
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

class ListEvents extends ListRecords
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

            Columns\SpatieTagsColumn::make('tags')
                ->label(__('Tags'))
                ->type("events")
                ->toggleable(),

            Columns\TextColumn::make('formatted_price')
                ->label(__('Price'))
                ->toggleable(),

            Columns\TextColumn::make('payment_type')
                ->label(__('Payment type'))
                ->enum(EventPaymentType::asSelectArray())
                ->toggleable(),

            Columns\ViewColumn::make('start_finish_date')
                ->view('tables.columns.event-datetime')
                ->label(__('Start/finish date'))
                ->getStateUsing(fn(Event $record) => $record)
                ->toggleable(),

            Columns\TextColumn::make('category.title')
                ->label(__('Category'))
                ->wrap()
                ->sortable()
                ->toggleable(),

            Columns\TextColumn::make('country')
                ->label(__('Country'))
                ->enum(Countries::asSelectArray())
                ->toggleable(),

            Columns\TextColumn::make('location')
                ->label(__('Location'))
                ->getStateUsing(fn($record): string => Locations::getDescription($record->country, $record->location))
                ->toggleable(),

            Columns\TextColumn::make('place_title')
                ->label(__('Place'))
                ->getStateUsing(function ($record) {
                    if ($record->address) {
                        return $record->address;
                    }

                    if ($record->place) {
                        return $record->place->title;
                    }

                    return null;
                })
                ->description(fn($record): ?string => ($record->place_slug && $record->address) ? __('Address specified') : null)
                ->wrap()
                ->sortable()
                ->toggleable(),

            Columns\BadgeColumn::make('status')
                ->label(__('Status'))
                ->enum(EventStatus::asSelectArray())
                ->sortable()
                ->colors([
                    'secondary' => EventStatus::CREATED,
                    'success' => EventStatus::PUBLISHED,
                    'danger' => EventStatus::REJECTED,
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

            Filters\Filter::make('status_and_visibility')
                ->form([
                    Components\Select::make('status')
                        ->label(__('Status'))
                        ->placeholder("-")
                        ->options(EventStatus::asSelectArray()),

                    Components\Select::make('visibility')
                        ->label(__('Visibility'))
                        ->placeholder("-")
                        ->options([
                            'true' => __("Yes"),
                            'false' => __("No"),
                        ]),
                ])
                ->columns()
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
                        $indicators['country'] = __('Status') . ' "' . EventStatus::getDescription($data['status']) . '"';
                    }

                    if ($data['visibility'] ?? null) {
                        $visibility = json_decode($data['visibility'])
                            ? __("Yes")
                            : __("No");
                        $indicators['visibility'] = __('Visibility') . ' "' . $visibility . '"';
                    }

                    return $indicators;
                }),

            Filters\Filter::make('payment_type_and_start_date')
                ->form([
                    Components\Select::make('payment_type')
                        ->label(__('Payment type'))
                        ->placeholder("-")
                        ->options(collect(EventPaymentType::asSelectArray())->put('no_payment_type', __("No"))),

                    Components\DatePicker::make('start_date')
                        ->label(__('Start date'))
                        ->displayFormat("j M Y")
                        ->placeholder("-"),
                ])
                ->columns()
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['payment_type'],
                            fn(Builder $query, $paymentType): Builder => $paymentType === "no_payment_type"
                                ? $query->whereNull('payment_type')
                                : $query->where('payment_type', $paymentType),
                        )
                        ->when(
                            $data['start_date'],
                            fn(Builder $query, $startDate): Builder => $query->where('start_date', $startDate),
                        );
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];

                    if ($data['payment_type'] ?? null) {
                        $paymentType = $data['payment_type'] === "no_payment_type"
                            ? __("No")
                            : EventPaymentType::getDescription($data['payment_type']);
                        $indicators['payment_type'] = __('Payment type') . ' "' . $paymentType . '"';
                    }

                    if ($data['start_date'] ?? null) {
                        $indicators['start_date'] = __('Start date') . ' ' . Carbon::parse($data['start_date'])
                                ->translatedFormat("j M Y");
                    }

                    return $indicators;
                }),

            Filters\Filter::make('country_and_location')
                ->form([
                    Components\Select::make('country')
                        ->label(__('Country'))
                        ->placeholder("-")
                        ->options(collect(Countries::asSelectArray())->put('no_country', __("No")))
                        ->reactive()
                        ->afterStateUpdated(fn(Closure $set) => $set('location', "")),

                    Components\Select::make('location')
                        ->label(__('Location'))
                        ->placeholder("-")
                        ->options(fn(Closure $get): Collection => collect(Locations::asSelectArray($get('country')))->put('no_location', __("No"))),
                ])
                ->columns()
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

            Filters\Filter::make('category_and_place')
                ->form([
                    Components\Select::make('category_id')
                        ->label(__('Category'))
                        ->placeholder("-")
                        ->options(EventCategory::orderBy('title')->get()->pluck('title', 'id')->put('no_category', __("No"))),

                    Components\Select::make('place_slug')
                        ->label(__('Place'))
                        ->placeholder("-")
                        ->options(Place::orderBy('title')->get()->pluck('title', 'slug')->put('no_place', __("No"))),
                ])
                ->columns()
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['category_id'],
                            fn(Builder $query, $categoryId): Builder => $categoryId === "no_category"
                                ? $query->whereNull('category_id')
                                : $query->where('category_id', $categoryId),
                        )
                        ->when(
                            $data['place_slug'],
                            fn(Builder $query, $placeSlug): Builder => $placeSlug === "no_place"
                                ? $query->whereNull('place_slug')
                                : $query->where('place_slug', $placeSlug),
                        );
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];

                    if ($data['category_id'] ?? null) {
                        $category = $data['category_id'] === "no_category"
                            ? __("No")
                            : EventCategory::find($data['category_id'])->title;
                        $indicators['category'] = __('Category') . ' "' . $category . '"';
                    }

                    if ($data['place_slug'] ?? null) {
                        $place = $data['place_slug'] === "no_place"
                            ? __("No")
                            : Place::bySlug($data['place_slug'])->first()->title;
                        $indicators['place'] = __('Place') . ' "' . $place . '"';
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
        return 'md';
    }
}

<?php

namespace App\Console\Commands;

use App\Models\ListingItem;
use App\Scopes\CountryScope;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ListingsItemsMovePrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'listings:items:move-price';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move price from meta to column';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $listingItems = ListingItem::withoutGlobalScope(CountryScope::class)
            ->withTrashed()
            ->get();

        $listingItems->each(function ($_item) {
            $rawData = collect(json_decode($_item->getRawOriginal('data'), true));
            $price = $rawData->pull('price');
            $currency = $rawData->pull('currency');

            $updateData = ['data' => json_encode($rawData->toArray())];

            if (!is_null($price)) {
                $updateData['price'] = $price;
            }

            if (!is_null($currency)) {
                $updateData['currency'] = $currency;
            }

            DB::table('listing_items')
                ->where('id', $_item->id)
                ->update($updateData);
        });

        $this->info("Price move completed");

        return Command::SUCCESS;
    }
}

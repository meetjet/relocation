<?php

namespace Database\Factories;

use App\Enums\ListingItemStatus;
use App\Facades\Cities;
use App\Facades\Countries;
use App\Models\ListingCategory;
use App\Models\ListingItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ListingItem>
 */
class ListingItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $country = $this->faker->randomElement(Countries::getValues());
        $city = $this->faker->randomElement(Cities::getValues($country));
        $price = $this->faker->randomNumber() + 10;
        $currency = $price ? config("countries.{$country}.currency.code") : null;

        return [
            'user_id' => 1,
            'category_id' => $this->faker->randomElement(array_merge(ListingCategory::all()->pluck('id')->toArray(), [null])),
            'country' => $country,
            'city' => $city,
            'title' => $this->faker->text(100),
            'description' => $this->faker->text(400),
//            'status' => $this->faker->randomElement(ListingItemStatus::getValues()),
            'status' => ListingItemStatus::PUBLISHED,
            'visibility' => $this->faker->boolean(),
            'price' => $price,
            'currency' => $currency,
        ];
    }

    /**
     * @param array $attributes
     * @param Model|null $parent
     * @return Collection
     */
    public function create($attributes = [], ?Model $parent = null): Collection
    {
        $listingItems = parent::create($attributes, $parent);

        $listingItems->each(function (ListingItem $_listingItem) {
            if ($_listingItem->status === ListingItemStatus::PUBLISHED) {
                $tags = $this->faker->words($this->faker->randomDigit());
                $_listingItem->attachTags($tags, "listing-items");
            }
        });

        return $listingItems;
    }
}

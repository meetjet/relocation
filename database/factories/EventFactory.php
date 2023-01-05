<?php

namespace Database\Factories;

use App\Enums\EventPaymentType;
use App\Enums\EventStatus;
use App\Facades\Locations;
use App\Facades\Countries;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Place;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Faq>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $country = $this->faker->randomElement(Countries::getValues());
        $location = $this->faker->randomElement(Locations::getValues($country));
        $status = EventStatus::PUBLISHED;
        $visibility = $status === EventStatus::PUBLISHED;
        $paymentType = $this->faker->randomElement(EventPaymentType::getValues());
        $price = ($paymentType !== EventPaymentType::FREE) ? $this->faker->randomNumber(3) : 0;
        $currency = config("countries.{$country}.currency.code");

        $placeSlug = $this->faker->randomElement([null, $this->faker->randomElement(Place::all()->pluck('slug')->toArray())]);
        $address = $placeSlug ? null : $this->faker->text(100);

        $startDate = Carbon::now()
            ->addDays($this->faker->numberBetween(0, 30))
            ->addHours($this->faker->numberBetween(0, 10));
        $finishDate = $this->faker->randomElement([null, Carbon::parse($startDate)->addHours($this->faker->numberBetween(2, 10))]);
        $finishTime = $finishDate ?: null;

        return [
            'user_id' => 1,
            'country' => $country,
            'location' => $location,
            'title' => $this->faker->text(100),
            'description' => $this->faker->text(400),
            'status' => $status,
            'visibility' => $visibility,
            'custom_nickname' => "common",
            'price' => $price,
            'currency' => $currency,
            'payment_type' => $paymentType,
            'place_slug' => $placeSlug,
            'address' => $address,
            'category_id' => $this->faker->randomElement(EventCategory::all()->pluck('id')->toArray()),
            'published_at' => $status === EventStatus::PUBLISHED ? now() : null,
            'start_date' => $startDate,
            'start_time' => $startDate,
            'finish_date' => $finishDate,
            'finish_time' => $finishTime,
        ];
    }

    /**
     * @param array $attributes
     * @param Model|null $parent
     * @return Collection
     */
    public function create($attributes = [], ?Model $parent = null): Collection
    {
        $events = parent::create($attributes, $parent);

        $events->each(function (Event $_event) {
            if ($_event->status === EventStatus::PUBLISHED) {
                // Tags
                $tags = collect($this->faker->words($this->faker->randomDigit()));
                $tags->push("common");
                $_event->attachTags($tags->toArray(), "events");
                // Image
                $_event->pictures()->create([
                    'content' => getFakeTransformCollection(),
                    'local_file_path' => null,
                    'tmp_image' => null,
                ]);
            }
        });

        return $events;
    }
}

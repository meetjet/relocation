<?php

namespace Database\Factories;

use App\Enums\EventPaymentType;
use App\Enums\EventStatus;
use App\Facades\Locations;
use App\Facades\Countries;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventPoint;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

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
        $status = $this->faker->randomElement([EventStatus::CREATED, EventStatus::CREATED, EventStatus::CREATED, EventStatus::REJECTED, EventStatus::PUBLISHED, EventStatus::PUBLISHED, EventStatus::PUBLISHED, EventStatus::PUBLISHED, EventStatus::PUBLISHED, EventStatus::PUBLISHED]);
        $visibility = $status === EventStatus::PUBLISHED;
        $paymentType = $this->faker->randomElement(EventPaymentType::getValues());
        $price = ($paymentType !== EventPaymentType::FREE) ? $this->faker->randomNumber(3) : 0;
        $currency = config("countries.{$country}.currency.code");

        $pointSlug = $this->faker->randomElement([null, $this->faker->randomElement(EventPoint::all()->pluck('slug')->toArray())]);
        $address = $pointSlug ? null : $this->faker->text(100);

        $startDate = $this->faker->date();
        $startTime = $this->faker->time();
        $finishDate = $this->faker->randomElement([null, $this->faker->date()]);
        $finishTime = $finishDate ? $this->faker->time() : null;

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
            'point_slug' => $pointSlug,
            'address' => $address,
            'category_id' => $this->faker->randomElement(EventCategory::all()->pluck('id')->toArray()),
            'published_at' => $status === EventStatus::PUBLISHED ? now() : null,
            'start_date' => $startDate,
            'start_time' => $startTime,
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
                $tags = collect($this->faker->words($this->faker->randomDigit()));
                $tags->push("common");
                $_event->attachTags($tags->toArray(), "events");
            }
        });

        return $events;
    }
}

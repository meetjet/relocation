<?php

namespace Database\Factories;

use App\Enums\Countries;
use App\Enums\EventStatus;
use App\Facades\Cities;
use App\Models\Event;
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
        $country = $this->faker->randomElement(array_merge(Countries::getValues(), [null]));
        $city = $this->faker->randomElement(array_merge(Cities::getValues($country), [null]));

        return [
            'user_id' => 1,
            'country' => $country,
            'city' => $city,
            'title' => $this->faker->text(100),
            'description' => $this->faker->text(400),
            'status' => $this->faker->randomElement(EventStatus::getValues()),
//            'status' => EventStatus::PUBLISHED,
            'visibility' => $this->faker->boolean(),
            'price' => $this->faker->randomNumber(),
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
                $tags = $this->faker->words($this->faker->randomDigit());
                $_event->attachTags($tags, "events");
            }
        });

        return $events;
    }
}

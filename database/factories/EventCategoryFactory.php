<?php

namespace Database\Factories;

use App\Enums\EventCategoryStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ListingCategory>
 */
class EventCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->words(asText: true),
            'description' => $this->faker->text(100),
            'status' => EventCategoryStatus::ACTIVE,
            'visibility' => true,
        ];
    }
}

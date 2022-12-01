<?php

namespace Database\Factories;

use App\Enums\EventPointStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ListingCategory>
 */
class EventPointFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->words(7, true),
            'description' => $this->faker->text(100),
            'address' => $this->faker->text(50),
            'status' => EventPointStatus::ACTIVE,
            'visibility' => true,
        ];
    }
}

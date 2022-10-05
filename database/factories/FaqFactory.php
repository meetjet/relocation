<?php

namespace Database\Factories;

use App\Enums\FaqStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Faq>
 */
class FaqFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $content = $this->faker->text();
        $status = $this->faker->randomElement(FaqStatus::getValues());
        $visibility = $this->faker->boolean();

        if ($status === FaqStatus::PUBLISHED) {
            return [
                'original' => $content,
                'question' => $content,
                'answer' => $this->faker->text(),
                'status' => $status,
                'visibility' => $visibility,
            ];
        }

        if ($status === FaqStatus::REJECTED) {
            return [
                'original' => $content,
                'status' => $status,
            ];
        }

        return [
            'original' => $content,
        ];

    }
}

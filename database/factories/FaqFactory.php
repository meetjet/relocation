<?php

namespace Database\Factories;

use App\Enums\FaqStatus;
use App\Models\Faq;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

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
        $content = $this->faker->text(400);
        $status = $this->faker->randomElement(FaqStatus::getValues());
        $visibility = $this->faker->boolean();

        if ($status === FaqStatus::PUBLISHED) {
            return [
                'user_id' => 1,
                'original' => $content,
                'title' => $this->faker->text(100),
                'question' => $content,
                'answer' => $this->faker->text(),
                'status' => $status,
                'visibility' => $visibility,
            ];
        }

        if ($status === FaqStatus::REJECTED) {
            return [
                'user_id' => 1,
                'original' => $content,
                'status' => $status,
            ];
        }

        return [
            'user_id' => 1,
            'original' => $content,
        ];
    }

    /**
     * @param array $attributes
     * @param Model|null $parent
     * @return Collection
     */
    public function create($attributes = [], ?Model $parent = null): Collection
    {
        $faqs = parent::create($attributes, $parent);

        $faqs->each(function (Faq $_faq) {
            if ($_faq->status === FaqStatus::PUBLISHED) {
                $tags = $this->faker->words($this->faker->randomDigit());
                $_faq->attachTags($tags, "faqs");
            }
        });

        return $faqs;
    }
}

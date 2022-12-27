<?php

namespace Database\Factories;

use App\Enums\FaqStatus;
use App\Facades\Countries;
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
        $country = $this->faker->randomElement(Countries::getValues());
        $status = FaqStatus::PUBLISHED;
        $visibility = $status === FaqStatus::PUBLISHED;

        if ($status === FaqStatus::PUBLISHED) {
            return [
                'user_id' => 1,
                'original' => $content,
                'country' => $country,
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
                'country' => $country,
                'status' => $status,
            ];
        }

        return [
            'user_id' => 1,
            'original' => $content,
            'country' => $country,
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
                $tags = collect($this->faker->words($this->faker->randomDigit()));
                $tags->push("common");
                $_faq->attachTags($tags->toArray(), "faqs");
            }
        });

        return $faqs;
    }
}

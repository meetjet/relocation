<?php

namespace Database\Factories;

use App\Enums\AdvertisementStatus;
use App\Facades\Countries;
use App\Models\Advertisement;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Advertisement>
 */
class AdvertisementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $country = $this->faker->randomElement(array_merge(Countries::getValues(), [null]));

        return [
            'user_id' => 1,
            'country' => $country,
            'title' => $this->faker->text(100),
            'description' => $this->faker->text(400),
            'status' => AdvertisementStatus::PUBLISHED,
            'visibility' => true,
        ];
    }

    /**
     * @param array $attributes
     * @param Model|null $parent
     * @return Collection
     */
    public function create($attributes = [], ?Model $parent = null): Collection
    {
        $advertisement = parent::create($attributes, $parent);

        $advertisement->each(function (Advertisement $_advert) {
            // Image
            $_advert->pictures()->create([
                'content' => getFakeTransformCollection(),
                'local_file_path' => null,
                'tmp_image' => null,
            ]);
        });

        return $advertisement;
    }
}

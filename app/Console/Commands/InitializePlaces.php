<?php

namespace App\Console\Commands;

use App\Enums\PlaceType;
use App\Models\Place;
use Illuminate\Console\Command;

class InitializePlaces extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'initialize:places';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize places';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $places = [
            [
                'slug' => "place-one",
                'title' => "Место проведения раз",
                'description' => "Описание первого места проведения",
                'type' => PlaceType::BAR,
                'country' => "armenia",
                'location' => "yerevan",
                'address_ru' => "Бейкер стрит, 221Б",
                'latitude' => 55.757131,
                'longitude' => 37.617114,
            ],
            [
                'slug' => "place-two",
                'title' => "Место проведения два",
                'description' => "Описание второго места проведения",
                'type' => PlaceType::RESTAURANT,
                'country' => "georgia",
                'location' => "tbilisi",
                'address_ru' => "Ул. Тисовая 4, Литтл Уингинг",
                'latitude' => 55.757131,
                'longitude' => 37.617114,
            ],
            [
                'slug' => "place-three",
                'title' => "Место проведения три",
                'description' => "Описание третьего места проведения",
                'type' => PlaceType::CAFE,
                'country' => "turkey",
                'location' => "istanbul",
                'address_ru' => "Авеню Лос Роблес, 2311Н, Пасадена",
                'latitude' => 55.757131,
                'longitude' => 37.617114,
            ],
        ];

        foreach ($places as $_place) {
            Place::updateOrCreate(
                [
                    'slug' => $_place['slug'],
                ],
                [
                    'title' => $_place['title'],
                    'description' => $_place['description'],
                    'type' => $_place['type'],
                    'country' => $_place['country'],
                    'location' => $_place['location'],
                    'address_ru' => $_place['address_ru'],
                    'latitude' => $_place['latitude'],
                    'longitude' => $_place['longitude'],
                    'visibility' => true,
                ]
            );
        }

        return Command::SUCCESS;
    }
}

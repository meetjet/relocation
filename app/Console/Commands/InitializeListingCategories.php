<?php

namespace App\Console\Commands;

use App\Models\ListingCategory;
use Illuminate\Console\Command;

class InitializeListingCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'initialize:listing-categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize categories for listing';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $categories = [
            [
                'slug' => "outfit",
                'title' => "Одежда, обувь и аксессуары",
            ],
            [
                'slug' => "home",
                'title' => "Для дома и дачи",
            ],
            [
                'slug' => "animals",
                'title' => "Животные",
            ],
            [
                'slug' => "real-estate",
                'title' => "Недвижимость",
            ],
            [
                'slug' => "auto",
                'title' => "Автомобили",
            ],
            [
                'slug' => "home-equipment",
                'title' => "Бытовая техника",
            ],
            [
                'slug' => "electronics",
                'title' => "Электроника",
            ],
            [
                'slug' => "special-equipment",
                'title' => "Специальная техника",
            ],
            [
                'slug' => "wellness",
                'title' => "Красота и здоровье",
            ],
            [
                'slug' => "other",
                'title' => "Прочее",
            ],
        ];

        foreach ($categories as $_category) {
            ListingCategory::updateOrCreate(
                [
                    'slug' => $_category['slug'],
                ],
                [
                    'title' => $_category['title'],
                ]
            );
        }

        return Command::SUCCESS;
    }
}

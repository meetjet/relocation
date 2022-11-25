<?php

namespace App\Console\Commands;

use App\Models\EventCategory;
use Illuminate\Console\Command;

class InitializeEventCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'initialize:event-categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize categories for events';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $categories = [
            [
                'slug' => "category-one",
                'title' => "Категория раз",
                'description' => "Описание первой категории",
            ],
            [
                'slug' => "category-two",
                'title' => "Категория два",
                'description' => "Описание второй категории",
            ],
            [
                'slug' => "category-three",
                'title' => "Категория три",
                'description' => "Описание третьей категории",
            ],
        ];

        foreach ($categories as $_category) {
            EventCategory::updateOrCreate(
                [
                    'slug' => $_category['slug'],
                ],
                [
                    'title' => $_category['title'],
                    'description' => $_category['description'],
                ]
            );
        }

        return Command::SUCCESS;
    }
}

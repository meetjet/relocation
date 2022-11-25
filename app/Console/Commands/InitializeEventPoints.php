<?php

namespace App\Console\Commands;

use App\Models\EventPoint;
use Illuminate\Console\Command;

class InitializeEventPoints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'initialize:event-points';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize points for events';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $categories = [
            [
                'slug' => "point-one",
                'title' => "Место проведения раз",
                'description' => "Описание первого места проведения",
                'address' => "Бейкер стрит, 221Б, Лондон, Великобритания",
            ],
            [
                'slug' => "point-two",
                'title' => "Место проведения два",
                'description' => "Описание второго места проведения",
                'address' => "Ул. Тисовая 4, Литтл Уингинг, Суррей, Великобритания",
            ],
            [
                'slug' => "point-three",
                'title' => "Место проведения три",
                'description' => "Описание третьего места проведения",
                'address' => "Авеню Лос Роблес, 2311Н, Пасадена, Калифорния, США",
            ],
        ];

        foreach ($categories as $_category) {
            EventPoint::updateOrCreate(
                [
                    'slug' => $_category['slug'],
                ],
                [
                    'title' => $_category['title'],
                    'description' => $_category['description'],
                    'address' => $_category['address'],
                ]
            );
        }

        return Command::SUCCESS;
    }
}

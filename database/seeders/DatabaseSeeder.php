<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Faq;
use App\Models\ListingItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        // Create a default admin user.
        User::factory()->create([
            'name' => 'Administrator',
            'email' => 'administrator@relocation.digital',
            'password' => Hash::make("password"),
        ]);

        User::factory()
            ->count(29)
            ->create();

        Faq::factory()
            ->count(100)
            ->create();

        Artisan::call('initialize:listing-categories');

        ListingItem::factory()
            ->count(100)
            ->create();

        Artisan::call('initialize:event-categories');
        Artisan::call('initialize:event-points');

        Event::factory()
            ->count(100)
            ->create();
    }
}

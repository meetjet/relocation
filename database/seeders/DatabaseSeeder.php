<?php

namespace Database\Seeders;

use App\Models\Faq;
//use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
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
            'email' => 'test@relocation.digital',
            'password' => Hash::make("test@relocation.digital"),
        ]);

        User::factory()
            ->count(29)
            ->create();

        Faq::factory()
            ->count(30)
            ->create();
    }
}

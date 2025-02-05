<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\User;
use Bavix\Wallet\Internal\Exceptions\ExceptionInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Jetstream\Features;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $faker = \Faker\Factory::create("ru_RU");

        $gender = $this->faker->randomElement(["male", "female"]);

        $firstName = $faker->firstName($gender);
        $lastName = $this->faker->randomElement(["", $faker->lastName($gender)]);
        $name = trim("{$firstName} {$lastName}");

        $email = $this->faker->unique()->safeEmail();
        $password = Hash::make('password');

        return [
            'name' => $name,
            'email' => $email,
            'email_verified_at' => now(),
            'password' => $password,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }

    /**
     * Indicate that the user should have a personal team.
     *
     * @return $this
     */
    public function withPersonalTeam()
    {
        if (! Features::hasTeamFeatures()) {
            return $this->state([]);
        }

        return $this->has(
            Team::factory()
                ->state(function (array $attributes, User $user) {
                    return ['name' => $user->name.'\'s Team', 'user_id' => $user->id, 'personal_team' => true];
                }),
            'ownedTeams'
        );
    }

    /**
     * @param array $attributes
     * @param Model|null $parent
     * @return User|Collection
     * @throws ExceptionInterface
     */
    public function create($attributes = [], ?Model $parent = null): User|Collection
    {
        $users = parent::create($attributes, $parent);

        $users->each(function (User $_user) {
            $balance = $this->faker->randomNumber(3);
            $_user->deposit($balance);
        });

        return $users;
    }
}

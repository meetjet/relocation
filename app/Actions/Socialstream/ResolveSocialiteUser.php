<?php

namespace App\Actions\Socialstream;

use Illuminate\Support\Facades\Log;
use JoelButcher\Socialstream\Contracts\ResolvesSocialiteUsers;
use JoelButcher\Socialstream\Socialstream;
use Laravel\Socialite\Contracts\User;
use Laravel\Socialite\Facades\Socialite;

class ResolveSocialiteUser implements ResolvesSocialiteUsers
{
    /**
     * Resolve the user for a given provider.
     *
     * @param string $provider
     * @return User
     */
    public function resolve(string $provider): User
    {
        $user = Socialite::driver($provider)->stateless()->user();

        if (Socialstream::generatesMissingEmails()) {
            $user->email = $user->getEmail() ?? ("{$user->id}@{$provider}.".config('app.domain'));
        }

        return $user;
    }
}

<?php

namespace App\Actions\Socialstream;

use Illuminate\Support\Facades\Log;
use JoelButcher\Socialstream\Contracts\ResolvesSocialiteUsers;
use JoelButcher\Socialstream\Socialstream;
use Laravel\Socialite\Facades\Socialite;

class ResolveSocialiteUser implements ResolvesSocialiteUsers
{
    /**
     * Resolve the user for a given provider.
     *
     * @param  string  $provider
     * @return \Laravel\Socialite\Contracts\User
     */
    public function resolve($provider)
    {
        try {
            $user = Socialite::driver($provider)->stateless()->user();
        } catch (\Throwable $throwable) {
            Log::debug('Socialite provider error: user not resolved: ' . $throwable->getMessage());
            return null;
        }

        if (Socialstream::generatesMissingEmails()) {
            $user->email = $user->getEmail() ?? ("{$user->id}@{$provider}.".config('app.domain'));
        }

        return $user;
    }
}

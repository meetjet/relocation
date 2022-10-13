<?php

namespace App\Telegram\Actions;

use App\Models\ConnectedAccount;
use App\Models\Team;
use App\Models\User;
use App\Telegram\Exceptions\UserRequiredException;
use Illuminate\Support\Facades\DB;
use JoelButcher\Socialstream\Socialstream;
use SergiX44\Nutgram\Telegram\Types\User\User as TelegramUser;
use Throwable;

class CreateUserAction
{
    /**
     * @param TelegramUser|null $telegramUser
     * @return int
     * @throws Throwable
     */
    public function execute(?TelegramUser $telegramUser): int
    {
        throw_if(is_null($telegramUser), UserRequiredException::class);

        $account = Socialstream::findConnectedAccountForProviderAndId("telegram", $telegramUser->id);

        if ($account) {
            return $account->user_id;
        }

        $user = User::where('email', $this->getEmail($telegramUser))->first();

        if ($user) {
            $this->CreateConnectedAccount($user, $telegramUser);
        } else {
            $user = DB::transaction(function () use ($telegramUser) {
                return tap(User::create([
                    'name' => $this->getName($telegramUser),
                    'email' => $this->getEmail($telegramUser),
                ]), function (User $user) use ($telegramUser) {
                    $user->markEmailAsVerified();

                    $this->CreateConnectedAccount($user, $telegramUser);

                    $user->ownedTeams()->save(Team::forceCreate([
                        'user_id' => $user->id,
                        'name' => explode(' ', $user->name, 2)[0] . "'s Team",
                        'personal_team' => true,
                    ]));
                });
            });
        }

        return $user->id;
    }

    /**
     * @param TelegramUser $telegramUser
     * @return string
     */
    private function getName(TelegramUser $telegramUser): string
    {
        $name = [
            $telegramUser->first_name,
            $telegramUser->last_name,
        ];

        return trim(implode(" ", $name));
    }

    /**
     * @param TelegramUser $telegramUser
     * @return string
     */
    private function getEmail(TelegramUser $telegramUser): string
    {
        return "{$telegramUser->id}@telegram." . config('app.domain');
    }

    /**
     * @param User $user
     * @param TelegramUser $telegramUser
     * @return ConnectedAccount
     */
    private function CreateConnectedAccount(User $user, TelegramUser $telegramUser): ConnectedAccount
    {
        return Socialstream::connectedAccountModel()::forceCreate([
            'user_id' => $user->id,
            'provider' => "telegram",
            'provider_id' => $telegramUser->id,
            'name' => $this->getName($telegramUser),
            'nickname' => $telegramUser->username,
            'email' => $this->getEmail($telegramUser),
        ]);
    }
}

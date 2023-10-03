<?php

namespace App\Providers;

use App\Models\ConnectedAccount;
use App\Models\Team;
use App\Models\User;
use App\Policies\ConnectedAccountPolicy;
use App\Policies\TeamPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Opcodes\LogViewer\LogFile;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Team::class => TeamPolicy::class,
        ConnectedAccount::class => ConnectedAccountPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Return true if the user is allowed access to the Log Viewer.
        Gate::define('viewLogViewer', static function (?User $user) {
            return $user && in_array($user->email, config('log-viewer.access_allowed_email'), true);
        });

        // Return true if the user is allowed to download the specific log file.
        Gate::define('downloadLogFile', static function (?User $user, LogFile $file) {
            return $user && in_array($user->email, config('log-viewer.download_allowed_email'), true);
        });

        // Return true if the user is allowed to delete the specific log file.
        Gate::define('deleteLogFile', static function (?User $user, LogFile $file) {
            return $user && in_array($user->email, config('log-viewer.delete_allowed_email'), true);
        });
    }
}

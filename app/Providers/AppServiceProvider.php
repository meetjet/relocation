<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Filament::serving(static function () {
            Filament::registerNavigationGroups([
                NavigationGroup::make()
                    ->label(__('Users'))
                    ->icon('heroicon-o-users')
                    ->collapsed(),
                NavigationGroup::make()
                    ->label(__('FAQ'))
                    ->icon('heroicon-o-collection')
                    ->collapsed(),
                NavigationGroup::make()
                    ->label(__('Flea market'))
                    ->icon('heroicon-o-lightning-bolt')
                    ->collapsed(),
                NavigationGroup::make()
                    ->label(__('Events'))
                    ->icon('heroicon-o-calendar')
                    ->collapsed(),
                NavigationGroup::make()
                    ->label(__('Places'))
                    ->icon('heroicon-o-map')
                    ->collapsed(),
                NavigationGroup::make()
                    ->label(__('Properties'))
                    ->icon('heroicon-o-office-building')
                    ->collapsed(),
            ]);
        });
    }
}

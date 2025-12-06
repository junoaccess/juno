<?php

namespace App\Providers;

use App\Enums\Role;
use App\Support\CurrentOrganization;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register CurrentOrganization as a singleton
        $this->app->singleton(CurrentOrganization::class, function () {
            return new CurrentOrganization;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Global authorization gate for ADMIN role
        // Users with the ADMIN role can perform any action
        Gate::before(function ($user, $ability) {
            // Check if the user has the ADMIN role in any organization
            return $user->roles()
                ->where('name', Role::ADMIN->value)
                ->exists() ? true : null;
        });
    }
}

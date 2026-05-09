<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('Admin')) {
                return true;
            }
            
            // Hierarchy logic: resource.manage allows all resource.action
            if (str_contains($ability, '.')) {
                $resource = explode('.', $ability)[0];
                try {
                    if ($user->hasPermissionTo($resource . '.manage')) {
                        return true;
                    }
                } catch (\Spatie\Permission\Exceptions\PermissionDoesNotExist $e) {
                    // Ignore if permission doesn't exist
                }
            }

            return null;
        });
    }
}

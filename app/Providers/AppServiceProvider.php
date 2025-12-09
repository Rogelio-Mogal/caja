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
    public function boot()
    {
        // Creando un superaministrador
        //$this->registerPolicies();
        Gate::before(function ($user, $ability){
            return $user->email == 'admin.mogal@gmail.com' ?? null;
        });
    }
}

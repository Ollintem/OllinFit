<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        // El rol Administrador pasa todas las verificaciones de permisos,
        // así nunca queda fuera de un módulo nuevo sin tener que marcarle cada permiso.
        Gate::before(fn ($user, $ability) => $user->hasRole('Administrador') ? true : null);
    }
}

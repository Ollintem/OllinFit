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
        // Evaluación de permisos con denegaciones por empleado:
        // 1) El Administrador pasa todo (nunca se auto-bloquea).
        // 2) Si el permiso está DENEGADO para el empleado, se bloquea aunque su rol lo otorgue.
        // 3) El resto cae en la evaluación normal de Spatie (rol + permisos directos).
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('Administrador')) {
                return true;
            }

            if ($user->hasDeniedPermission($ability)) {
                return false;
            }

            return null;
        });
    }
}

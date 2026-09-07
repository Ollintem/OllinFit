<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\RoleController; // <-- 1. Importamos el controlador de Roles
use Illuminate\Support\Facades\Route;

// Ruta principal: Mandar directo al Login
Route::get('/', function () {
    return view('auth.login');
});

// Todas las rutas del sistema van dentro de este grupo seguro
Route::middleware(['auth', 'active'])->group(function () {
    
    // Dashboard principal
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // =========================================================
    // MÓDULOS CON PROTECCIÓN GRANULAR (RBAC)
    // =========================================================

    // MÓDULO: EMPLEADOS (Alias: usuarios)
    // Listado (mostrar)
    Route::get('empleados', [EmployeeController::class, 'index'])
        ->name('empleados.index')
        ->middleware('permission:mostrar_empleados');

    // Alta (alta)
    Route::get('empleados/create', [EmployeeController::class, 'create'])
        ->name('empleados.create')
        ->middleware('permission:crear_empleados');
        
    Route::post('empleados', [EmployeeController::class, 'store'])
        ->name('empleados.store')
        ->middleware('permission:crear_empleados');

    // Edición (editar)
    Route::get('empleados/{empleado}/edit', [EmployeeController::class, 'edit'])
        ->name('empleados.edit')
        ->middleware('permission:editar_empleados');
        
    Route::put('empleados/{empleado}', [EmployeeController::class, 'update'])
        ->name('empleados.update')
        ->middleware('permission:editar_empleados');

    // Eliminación (eliminar)
    Route::delete('empleados/{empleado}', [EmployeeController::class, 'destroy'])
        ->name('empleados.destroy')
        ->middleware('permission:eliminar_empleados');


    // =========================================================
    // MÓDULO: ROLES Y PERMISOS
    // =========================================================
    
    // Alta (alta)
    Route::get('roles/create', [RoleController::class, 'create'])
        ->name('roles.create');
        
    Route::post('roles', [RoleController::class, 'store'])
        ->name('roles.store');
        
    // Nota: Más adelante podemos agregarle los middlewares de permission 
    // a estas rutas (ej. 'crear_roles', 'mostrar_roles') igual que a empleados.

});

require __DIR__.'/auth.php';
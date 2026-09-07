<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\RoleController; // <-- 1. Importamos el controlador de Roles
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlanController;

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


    // ========================================================================
    // MÓDULO: ROLES Y PERMISOS
    // ========================================================================

    // Alta (crear y guardar)
    Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('roles', [RoleController::class, 'store'])->name('roles.store');

    // Edición y Actualización
    Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');

    // Eliminación
    Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
        

    // =========================================================
    // MÓDULO: PLANES Y MEMBRESÍAS
    // =========================================================
    
    // MÓDULO: PLANES Y MEMBRESÍAS
    Route::get('planes', [PlanController::class, 'index'])->name('planes.index')->middleware('permission:mostrar_planes');
    Route::get('planes/create', [PlanController::class, 'create'])->name('planes.create')->middleware('permission:crear_editar_planes');
    Route::post('planes', [PlanController::class, 'store'])->name('planes.store')->middleware('permission:crear_editar_planes');
    Route::get('planes/{plan}/edit', [PlanController::class, 'edit'])->name('planes.edit')->middleware('permission:crear_editar_planes');
    Route::put('planes/{plan}', [PlanController::class, 'update'])->name('planes.update')->middleware('permission:crear_editar_planes');
    Route::delete('planes/{plan}', [PlanController::class, 'destroy'])->name('planes.destroy')->middleware('permission:eliminar_planes');

    // Nota: Más adelante podemos agregarle los middlewares de permission 
    // a estas rutas (ej. 'crear_roles', 'mostrar_roles') igual que a empleados.

});

require __DIR__.'/auth.php';
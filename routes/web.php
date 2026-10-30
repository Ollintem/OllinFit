<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\RoleController; // <-- 1. Importamos el controlador de Roles
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CashRegisterController;
use App\Http\Controllers\DashboardController;


// Ruta principal: Mandar directo al Login
Route::get('/', function () {
    return view('auth.login');
});

// Todas las rutas del sistema van dentro de este grupo seguro
Route::middleware(['auth', 'active'])->group(function () {

    // Dashboard principal con métricas dinámicas
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('permission:ver_dashboard')->name('dashboard');

    // MÓDULOS CON PROTECCIÓN GRANULAR (RBAC)
    // =========================================================

    // MÓDULO: EMPLEADOS (Alias: usuarios)
    // Listado (mostrar)
    Route::get('empleados', [EmployeeController::class, 'index'])
        ->name('empleados.index')
        ->middleware('permission:ver_empleados');

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
    // Solo el rol Administrador con el permiso 'gestionar_roles' puede tocarlo.
    // ========================================================================
    Route::middleware(['permission:gestionar_roles', 'role:Administrador'])->group(function () {
        // Alta (crear y guardar)
        Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('roles', [RoleController::class, 'store'])->name('roles.store');

        // Edición y Actualización
        Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');

        // Eliminación
        Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });


    // =========================================================
    // MÓDULO: PLANES Y MEMBRESÍAS
    // =========================================================

    // MÓDULO: PLANES Y MEMBRESÍAS
    Route::get('planes', [PlanController::class, 'index'])->name('planes.index')->middleware('permission:ver_planes');
    Route::get('planes/create', [PlanController::class, 'create'])->name('planes.create')->middleware('permission:crear_planes');
    Route::post('planes', [PlanController::class, 'store'])->name('planes.store')->middleware('permission:crear_planes');
    Route::get('planes/{plan}/edit', [PlanController::class, 'edit'])->name('planes.edit')->middleware('permission:editar_planes');
    Route::put('planes/{plan}', [PlanController::class, 'update'])->name('planes.update')->middleware('permission:editar_planes');
    Route::delete('planes/{plan}', [PlanController::class, 'destroy'])->name('planes.destroy')->middleware('permission:eliminar_planes');


   // =========================================================
    // 🛡️ RUTAS PROTEGIDAS POR CAJA ABIERTA (Solo pasan si hay caja)
    // =========================================================
    Route::middleware([\App\Http\Middleware\CheckCashRegisterOpen::class])->group(function () {
        
        // 1. Registro de Nuevos Socios (RUTAS ESTÁTICAS ARRIBA)
        Route::get('socios/create', [MemberController::class, 'create'])->name('socios.create')->middleware('permission:crear_socios');
        Route::post('socios', [MemberController::class, 'store'])->name('socios.store')->middleware(['permission:crear_socios', 'throttle:30,1']);

        // 2. Renovaciones de Membresías
        Route::post('socios/{id}/renovar', [MemberController::class, 'renovarMensualidad'])->name('socios.renovar')->middleware(['permission:renovar_socios', 'throttle:30,1']);
        Route::post('socios/{member}/renovar', [MemberController::class, 'renew'])->name('socios.renew')->middleware(['permission:renovar_socios', 'throttle:30,1']);

        // 3. Punto de Venta y Tienda (Cobra ingresos extras)
        Route::get('punto-de-venta', [SaleController::class, 'index'])->name('sales.index')->middleware('permission:usar_pos');
        Route::post('punto-de-venta', [SaleController::class, 'store'])->name('sales.store')->middleware(['permission:usar_pos', 'throttle:30,1']);

       // 4. Gastos y Compras (Salidas de efectivo)
        Route::get('gastos', [App\Http\Controllers\ExpenseController::class, 'index'])->name('gastos.index')->middleware('permission:ver_gastos');
        Route::get('gastos/create', [App\Http\Controllers\ExpenseController::class, 'create'])->name('gastos.create')->middleware('permission:crear_gastos');
        Route::post('gastos', [App\Http\Controllers\ExpenseController::class, 'store'])->name('gastos.store')->middleware(['permission:crear_gastos', 'throttle:30,1']);
        // NUEVAS RUTAS: Editar, Actualizar y Eliminar
        Route::get('gastos/{gasto}/edit', [App\Http\Controllers\ExpenseController::class, 'edit'])->name('gastos.edit')->middleware('permission:editar_gastos');
        Route::put('gastos/{gasto}', [App\Http\Controllers\ExpenseController::class, 'update'])->name('gastos.update')->middleware('permission:editar_gastos');
        Route::delete('gastos/{id}', [App\Http\Controllers\ExpenseController::class, 'destroy'])->name('gastos.destroy')->middleware('permission:eliminar_gastos');
    });

    // =========================================================
    // MÓDULO: SOCIOS (Rutas libres que NO cobran)
    // =========================================================
    Route::get('socios', [MemberController::class, 'index'])->name('socios.index')->middleware('permission:ver_socios');
    Route::get('socios/{id}/datos-renovacion', [MemberController::class, 'obtenerDatosRenovacion'])->middleware('permission:renovar_socios');

    // RUTAS DINÁMICAS (Con variables)
    Route::get('socios/{member}/edit', [MemberController::class, 'edit'])->name('socios.edit')->middleware('permission:editar_socios');
    Route::put('socios/{member}', [MemberController::class, 'update'])->name('socios.update')->middleware('permission:editar_socios');
    Route::delete('socios/{member}', [MemberController::class, 'destroy'])->name('socios.destroy')->middleware('permission:eliminar_socios');
    Route::get('socios/{member}/ticket', [MemberController::class, 'ticket'])->name('socios.ticket')->middleware('permission:ver_socios');
    Route::get('socios/{member}/pagos', [MemberController::class, 'payments'])->name('socios.pagos')->middleware('permission:ver_socios');
    Route::get('socios/{member}/accesos', [MemberController::class, 'accesses'])->name('socios.accesos')->middleware('permission:ver_socios');

    // ⚠️ ATENCIÓN: Esta ruta es un "comodín" ({member}).
    // DEBE IR SIEMPRE HASTA EL FINAL para no chocar con las demás.
    Route::get('socios/{member}', [MemberController::class, 'show'])->name('socios.show')->middleware('permission:ver_socios');

    // MÓDULO: CONTROL DE ACCESO
    Route::get('/acceso', [App\Http\Controllers\AccessController::class, 'index'])->name('acceso.index')->middleware('permission:ver_acceso');
    Route::post('/acceso/escanear', [App\Http\Controllers\AccessController::class, 'scan'])->name('acceso.scan')->middleware(['permission:operar_acceso', 'throttle:60,1']);
    Route::post('/acceso/liberar/{member}', [App\Http\Controllers\AccessController::class, 'release'])->name('acceso.release')->middleware(['permission:operar_acceso', 'throttle:60,1']);



    // =========================================================
    // MÓDULO: PUNTO DE VENTA (INVENTARIO DE PRODUCTOS)
    // =========================================================
    Route::get('productos', [ProductController::class, 'index'])->name('productos.index')->middleware('permission:ver_productos');
    Route::get('productos/create', [ProductController::class, 'create'])->name('productos.create')->middleware('permission:crear_productos');
    Route::post('productos', [ProductController::class, 'store'])->name('productos.store')->middleware('permission:crear_productos');
    Route::get('productos/{product}/edit', [ProductController::class, 'edit'])->name('productos.edit')->middleware('permission:editar_productos');
    Route::put('productos/{product}', [ProductController::class, 'update'])->name('productos.update')->middleware('permission:editar_productos');
    Route::delete('productos/{product}', [ProductController::class, 'destroy'])->name('productos.destroy')->middleware('permission:eliminar_productos');

   // =========================================================
    // MÓDULO: PROVEEDORES
    // =========================================================
    Route::get('proveedores', [App\Http\Controllers\SupplierController::class, 'index'])->name('proveedores.index')->middleware('permission:ver_proveedores');
    Route::post('proveedores', [App\Http\Controllers\SupplierController::class, 'store'])->name('proveedores.store')->middleware('permission:crear_proveedores');
    Route::put('proveedores/{proveedor}', [App\Http\Controllers\SupplierController::class, 'update'])->name('proveedores.update')->middleware('permission:editar_proveedores');
    Route::delete('proveedores/{proveedor}', [App\Http\Controllers\SupplierController::class, 'destroy'])->name('proveedores.destroy')->middleware('permission:eliminar_proveedores');


    // =========================================================
    // MÓDULO: PUNTO DE VENTA (VENTAS / CAJA)
    // =========================================================
    Route::get('punto-de-venta/{sale}/ticket', [SaleController::class, 'ticket'])->name('sales.ticket')->middleware('permission:usar_pos');

    // MÓDULO: REPORTES
    Route::get('/reportes', [ReportController::class, 'index'])->name('reports.index')->middleware('permission:ver_reportes');
    Route::get('/reportes/exportar', [ReportController::class, 'exportarPdf'])->name('reports.export')->middleware('permission:exportar_reportes');

    // MÓDULO: CAJA
    Route::get('/flujo-de-caja', [CashRegisterController::class, 'index'])->name('caja.index')->middleware('permission:ver_caja');
    Route::post('/flujo-de-caja/abrir', [CashRegisterController::class, 'open'])->name('caja.open')->middleware(['permission:operar_caja', 'throttle:30,1']);
    Route::post('/flujo-de-caja/cerrar', [CashRegisterController::class, 'close'])->name('caja.close')->middleware(['permission:operar_caja', 'throttle:30,1']);
    Route::get('/flujo-de-caja/historial', [CashRegisterController::class, 'historial'])->name('caja.historial')->middleware('permission:ver_caja');

    // Ruta para generar el modal dinámico
    Route::get('punto-de-venta/{sale}/datos-ticket', [SaleController::class, 'getTicketData'])->name('sales.ticket.data')->middleware('permission:usar_pos');
    Route::get('ventas/historial', [SaleController::class, 'history'])->name('sales.history')->middleware('permission:ver_historial_ventas');

});

require __DIR__ . '/auth.php';
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Permisos, rol Administrador y roles de demo (fuente de verdad: config/permisos.php)
        $this->call(RolePermissionSeeder::class);

        // 2. Empleados de demo (uno por rol, incluido uno inactivo) — requiere los roles ya creados
        $this->call(EmployeeSeeder::class);

        // 3. Datos de catálogo y demo
        $this->call([
            PlanSeeder::class,
            MemberSeeder::class,
            SupplierSeeder::class,
            ProductSeeder::class,
        ]);
    }
}
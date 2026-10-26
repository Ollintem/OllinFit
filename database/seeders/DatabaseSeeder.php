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
        // 1. Permisos y rol Administrador (fuente de verdad: config/permisos.php)
        $this->call(RolePermissionSeeder::class);

        // 2. Datos de catálogo y demo
        $this->call([
            PlanSeeder::class,
            MemberSeeder::class,
        ]);
    }
}
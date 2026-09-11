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
        // Limpiar la caché de permisos de Spatie
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Definir TODOS los permisos de la Matriz del formulario
        $permisos = [
            'mostrar_dashboard',
            'mostrar_socios', 'crear_editar_socios', 'eliminar_socios',
            'mostrar_planes', 'crear_editar_planes', 'eliminar_planes',
            'mostrar_reportes',
            'mostrar_empleados', 'crear_empleados', 'editar_empleados', 'eliminar_empleados',
            'mostrar_configuracion', 'editar_configuracion',
        ];

        // 2. Crearlos en la base de datos
        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        // 3. Crear el rol de Administrador y darle TODOS los permisos
        $adminRole = Role::firstOrCreate([
            'name' => 'Administrador',
            'description' => 'Acceso total a todos los módulos del sistema.'
        ]);
        $adminRole->syncPermissions(Permission::all());

        // 4. Crear al administrador principal
        $user = User::firstOrCreate(
            ['email' => 'admin@ollinfit.com'],
            [
                'name' => 'Eduardo',
                'last_name' => 'Gómez',
                'password' => Hash::make('123'),
                'is_active' => true,
            ]
        );

        // 5. Asignarle el rol al usuario
        $user->assignRole($adminRole);

        // =========================================================
        // 6. ¡NUEVO! Llamar a los seeders de Planes y Socios
        // =========================================================
        $this->call([
            PlanSeeder::class,
            MemberSeeder::class,
        ]);
    }
}
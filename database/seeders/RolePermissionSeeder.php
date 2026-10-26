<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Siembra los permisos y el rol Administrador a partir de la matriz
 * definida en config/permisos.php (fuente única de verdad).
 *
 * Se puede ejecutar aislado sin tocar datos de negocio:
 *   php artisan db:seed --class=RolePermissionSeeder
 */
class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // 1. Reunir todos los permisos de la matriz
        $permisos = collect(config('permisos'))
            ->flatMap(fn ($modulo) => array_keys($modulo['permisos']))
            ->unique()
            ->values();

        // 2. Crear los que falten
        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        // 3. Podar los permisos viejos que ya no están en la matriz
        //    (limpia los antiguos mostrar_* / crear_editar_* / configuracion)
        Permission::whereNotIn('name', $permisos)->delete();

        // 4. El Administrador conserva todos los permisos
        $adminRole = Role::firstOrCreate(
            ['name' => 'Administrador'],
            ['description' => 'Acceso total a todos los módulos del sistema.']
        );
        $adminRole->syncPermissions(Permission::all());

        // 5. Asegurar el usuario administrador principal
        $admin = User::firstOrCreate(
            ['email' => 'admin@ollinfit.com'],
            [
                'name' => 'Eduardo',
                'last_name' => 'Gómez',
                'password' => Hash::make('123'),
                'is_active' => true,
            ]
        );
        $admin->assignRole($adminRole);

        // 6. Roles adicionales con subconjuntos de permisos (para probar el RBAC y el aterrizaje por rol)
        $rolesExtra = [
            'Gerente' => [
                'descripcion' => 'Acceso a todo excepto la gestión de roles.',
                // Todos los permisos de la matriz menos gestionar_roles
                'permisos' => $permisos->reject(fn ($p) => $p === 'gestionar_roles')->values()->all(),
            ],
            'Recepcionista' => [
                'descripcion' => 'Atención en recepción: socios, cobros de membresía, caja y control de acceso.',
                'permisos' => [
                    'ver_dashboard',
                    'ver_socios', 'crear_socios', 'editar_socios', 'renovar_socios',
                    'ver_planes',
                    'usar_pos',
                    'ver_caja', 'operar_caja',
                    'ver_acceso', 'operar_acceso',
                ],
            ],
            'Cajero' => [
                'descripcion' => 'Punto de venta, caja y gastos. Sin acceso al dashboard (aterriza en el POS).',
                'permisos' => [
                    'usar_pos', 'ver_historial_ventas',
                    'ver_caja', 'operar_caja',
                    'ver_gastos', 'crear_gastos',
                    'ver_productos',
                ],
            ],
            'Entrenador' => [
                'descripcion' => 'Solo control de acceso (aterriza en la pantalla de acceso).',
                'permisos' => [
                    'ver_acceso', 'operar_acceso',
                ],
            ],
        ];

        foreach ($rolesExtra as $nombre => $datos) {
            $rol = Role::firstOrCreate(['name' => $nombre], ['description' => $datos['descripcion']]);
            $rol->syncPermissions($datos['permisos']);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}

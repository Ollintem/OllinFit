<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Empleados de demostración, uno por rol, para probar el RBAC, el aterrizaje
 * por rol tras el login (User::homeRoute) y el bloqueo de cuentas inactivas.
 * Debe ejecutarse DESPUÉS de RolePermissionSeeder (los roles ya existen).
 */
class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        // email => [nombre, apellido, rol, activo]
        $empleados = [
            'gerente@ollinfit.com'    => ['Laura',  'Mendoza', 'Gerente',       true],
            'recepcion@ollinfit.com'  => ['Sofía',  'Ramírez', 'Recepcionista', true],
            'cajero@ollinfit.com'     => ['Diego',  'Torres',  'Cajero',        true],
            'entrenador@ollinfit.com' => ['Marco',  'Flores',  'Entrenador',    true],
            // Empleado dado de baja: debe quedar bloqueado por CheckActiveUser
            'exempleado@ollinfit.com' => ['Pedro',  'Vázquez', 'Cajero',        false],
        ];

        foreach ($empleados as $email => [$nombre, $apellido, $rol, $activo]) {
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $nombre,
                    'last_name' => $apellido,
                    'password' => Hash::make('password123'),
                    'is_active' => $activo,
                ]
            );
            $user->syncRoles([$rol]);
        }
    }
}

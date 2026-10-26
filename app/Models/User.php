<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles; // <-- 1. IMPORTACIÓN DE ROLES

// 2. AGREGAMOS LOS NUEVOS CAMPOS A FILLABLE
#[Fillable([
    'name', 
    'email', 
    'password', 
    'last_name', 
    'phone', 
    'hire_date', 
    'profile_photo_path', 
    'is_active'
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles; // <-- 3. ACTIVAMOS LOS ROLES AQUÍ

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean', // Buena práctica: castear a booleano
        ];
    }

    /**
     * Ruta de aterrizaje tras iniciar sesión: el primer módulo (en orden de
     * preferencia) al que el usuario tiene permiso de acceso. Así un empleado
     * sin 'ver_dashboard' no cae en un 403 al entrar.
     *
     * Devuelve el nombre de la ruta, o null si no tiene ningún módulo asignado.
     */
    public function homeRoute(): ?string
    {
        // permiso => ruta de inicio del módulo, en orden de prioridad
        $modulos = [
            'ver_dashboard'   => 'dashboard',
            'ver_socios'      => 'socios.index',
            'usar_pos'        => 'sales.index',
            'ver_caja'        => 'caja.index',
            'ver_productos'   => 'productos.index',
            'ver_gastos'      => 'gastos.index',
            'ver_proveedores' => 'proveedores.index',
            'ver_planes'      => 'planes.index',
            'ver_acceso'      => 'acceso.index',
            'ver_reportes'    => 'reports.index',
            'ver_empleados'   => 'empleados.index',
        ];

        foreach ($modulos as $permiso => $ruta) {
            if ($this->can($permiso)) {
                return $ruta;
            }
        }

        return null;
    }
}
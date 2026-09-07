<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    // 1. Mostrar el formulario (este ya lo tenías)
    public function create()
    {
        return view('roles.create');
    }

    // 2. Guardar el rol y sus permisos
    public function store(Request $request)
    {
        // 1. Validar los datos recibidos
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name', // Que no se repita el nombre
            'permissions' => 'nullable|array', // Las casillas seleccionadas (puede venir vacío)
        ]);

        // 2. Crear el Rol en la base de datos
        $role = Role::create([
            'name' => $request->name,
            'description' => $request->description, // <-- Lo dejamos comentado hasta crear la migración
        ]);

        // 3. Asignar los permisos seleccionados usando la magia de Spatie
        if ($request->has('permissions')) {
            // syncPermissions limpia permisos viejos y pone los nuevos automáticamente
            $role->syncPermissions($request->permissions);
        }

        // 4. Redirigir de regreso a la pantalla principal con un mensaje
        return redirect()->route('empleados.index')->with('success', 'Rol creado y permisos asignados correctamente.');
    }
}
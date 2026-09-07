<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission; // Importante para cargar los permisos

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
    // Mostrar el formulario de edición
    public function edit(Role $role)
    {
        // Bloqueo de seguridad: Evitar que alguien edite el rol Administrador por URL
        if ($role->name === 'Administrador') {
            return redirect()->route('empleados.index')->with('error', 'El rol de Administrador no se puede editar.');
        }

        // Traemos todos los permisos que existen en la base de datos
        $permissions = Permission::all();
        
        return view('roles.edit', compact('role', 'permissions'));
    }

    // Actualizar el rol y sus permisos
    public function update(Request $request, Role $role)
    {
        // 1. Validar datos (que el nombre no se repita con otro rol)
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array' // array de IDs de permisos
        ]);

        // 2. Actualizar el nombre del rol
        $role->update(['name' => $request->name]);

        // 3. Sincronizar los permisos (Spatie automáticamente quita los desmarcados y agrega los nuevos)
        $role->syncPermissions($request->permissions ?? []);

        // 4. Redirigir de regreso a la pantalla de empleados/roles
        return redirect()->route('empleados.index')->with('success', 'Rol y permisos actualizados correctamente.');
    }
    // Eliminar el rol
    public function destroy(Role $role)
    {
        // Bloqueo de seguridad nivel Dios: Impedir que borren el rol de Administrador
        if ($role->name === 'Administrador') {
            return redirect()->route('empleados.index')->with('error', 'Por seguridad, el rol de Administrador no se puede eliminar.');
        }

        // Eliminamos el rol
        $role->delete();

        // Regresamos a la pantalla con mensaje de éxito
        return redirect()->route('empleados.index')->with('success', 'Rol eliminado correctamente.');
    }
}
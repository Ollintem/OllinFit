<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage; // <-- Importante para borrar fotos viejas
use Spatie\Permission\Models\Role;

class EmployeeController extends Controller
{
    // 1. Mostrar la lista de empleados
    public function index()
    {
        $employees = User::all(); 
        $roles = Role::all();
        return view('employees.index', compact('employees', 'roles'));
    }

    // 2. Mostrar el formulario para crear un empleado
    public function create()
    {
        $roles = Role::all();
        return view('employees.create', compact('roles'));
    }

    // 3. Guardar el nuevo empleado
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|exists:roles,name', 
            'phone' => 'nullable|string|max:15',
            'hire_date' => 'nullable|date',
            'profile_photo_path' => 'nullable|image|mimes:jpeg,png,webp|max:2048', 
        ]);

        $photoPath = null;
        if ($request->hasFile('profile_photo_path')) {
            $photoPath = $request->file('profile_photo_path')->store('profile-photos', 'public');
        }

        $user = User::create([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password), 
            'phone' => $request->phone,
            'hire_date' => $request->hire_date,
            'is_active' => $request->has('is_active') ? true : false, 
            'profile_photo_path' => $photoPath,
        ]);

        $user->assignRole($request->role);

        return redirect()->route('empleados.index')->with('success', 'Empleado registrado correctamente.');
    }

    // 4. Mostrar el formulario de edición
    public function edit(User $empleado)
    {
        $roles = Role::all();
        // Mandamos los datos del empleado y los roles disponibles a la vista de edición
        return view('employees.edit', compact('empleado', 'roles'));
    }

    // 5. Actualizar los datos en la base de datos
    public function update(Request $request, User $empleado)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            // unique:users,email,X permite que guarde su mismo correo sin marcar error
            'email' => 'required|string|email|max:255|unique:users,email,' . $empleado->id,
            'password' => 'nullable|string|min:8', // Nullable = Opcional
            'role' => 'required|exists:roles,name',
            'phone' => 'nullable|string|max:15',
            'hire_date' => 'nullable|date',
            'profile_photo_path' => 'nullable|image|mimes:jpeg,png,webp|max:2048', 
        ]);

        // Preparamos los datos básicos (excluyendo campos especiales)
        $data = [
            'name' => $request->name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'hire_date' => $request->hire_date,
            'is_active' => $request->has('is_active') ? true : false,
        ];

        // Solo actualizamos la contraseña si el administrador escribió una nueva
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Si subió una foto nueva...
        if ($request->hasFile('profile_photo_path')) {
            // Borramos la foto anterior del servidor (si tenía una)
            if ($empleado->profile_photo_path) {
                Storage::disk('public')->delete($empleado->profile_photo_path);
            }
            // Guardamos la nueva foto
            $data['profile_photo_path'] = $request->file('profile_photo_path')->store('profile-photos', 'public');
        }

        // Actualizamos al empleado
        $empleado->update($data);

        // Actualizamos su rol en Spatie (syncRoles borra el anterior y asigna el nuevo)
        $empleado->syncRoles([$request->role]);

        return redirect()->route('empleados.index')->with('success', 'Datos del empleado actualizados.');
    }

    // 6. Eliminar al empleado
    public function destroy(User $empleado)
    {
        // Borramos su foto del servidor para liberar espacio
        if ($empleado->profile_photo_path) {
            Storage::disk('public')->delete($empleado->profile_photo_path);
        }

        // Eliminamos el registro de la base de datos
        $empleado->delete();

        return redirect()->route('empleados.index')->with('success', 'Empleado eliminado del sistema.');
    }
}
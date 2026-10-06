<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    // 1. Mostrar el catálogo de planes
   public function index()
    {
        // Traemos los planes y contamos los socios cuya fecha de vencimiento es igual o mayor a hoy
        $planes = \App\Models\Plan::withCount(['members' => function ($query) {
            $query->whereDate('expiration_date', '>=', now());
        }])->get();

        return view('planes.index', compact('planes'));
    }

    // 2. Mostrar el formulario para crear un plan
    public function create()
    {
        return view('planes.create');
    }

    // 3. Guardar el plan en la base de datos
    public function store(Request $request)
    {
        // Validar los datos
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'description' => 'nullable|string|max:500',
        ]);

        // Crear el plan (Asignamos un color por defecto que luego podremos editar)
        Plan::create([
            'name' => $request->name,
            'price' => $request->price,
            'duration_days' => $request->duration_days,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
            'color' => 'blue', // Color por defecto
        ]);
        

        return redirect()->route('planes.index')->with('success', 'Plan creado exitosamente.');
    }
    // 4. Mostrar el formulario de edición
    public function edit(Plan $plan)
    {
        return view('planes.edit', compact('plan'));
    }

    // 5. Actualizar los datos en la base de datos
    public function update(Request $request, Plan $plan)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'description' => 'nullable|string|max:500',
            'highlight_text' => 'nullable|string|max:255',
            'color' => 'required|string|in:blue,orange,green,purple,gray',
        ]);

        $plan->update([
            'name' => $request->name,
            'price' => $request->price,
            'duration_days' => $request->duration_days,
            'description' => $request->description,
            'highlight_text' => $request->highlight_text,
            'color' => $request->color,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('planes.index')->with('success', 'Plan actualizado exitosamente.');
    }

    // 6. Eliminar el plan
   public function destroy($id)
    {
        try {
            $plan = \App\Models\Plan::findOrFail($id);
            
            // Intenta eliminar el plan de la base de datos
            $plan->delete();

            return redirect()->route('planes.index')->with('success', 'El plan fue eliminado correctamente del sistema.');

        } catch (\Illuminate\Database\QueryException $e) {
            // SEGURIDAD: Si el plan ya fue asignado a uno o más socios, la BD rechazará el borrado.
            return redirect()->route('planes.index')->with('error', '🛡️ ¡ALERTA DE SEGURIDAD! No se puede eliminar este plan porque existen socios (activos o inactivos) que lo están usando o lo usaron en el pasado. Para dejar de ofrecerlo, te recomendamos editarlo y cambiar su estado a "Inactivo".');
            
        } catch (\Exception $e) {
            return redirect()->route('planes.index')->with('error', 'Ocurrió un error inesperado al intentar eliminar el plan.');
        }
    }
}
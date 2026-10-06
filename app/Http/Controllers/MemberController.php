<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        // 1. Atrapamos lo que el usuario escribió en el buscador
        $busqueda = $request->input('search');

        // 2. Hacemos la consulta inteligente
        $socios = Member::with('plan')
            ->when($busqueda, function ($query, $busqueda) {
                // Si hay texto, buscamos coincidencias
                return $query->where('name', 'LIKE', "%{$busqueda}%")
                             ->orWhere('last_name', 'LIKE', "%{$busqueda}%")
                             ->orWhere('folio', 'LIKE', "%{$busqueda}%");
            })
            // Ordenamos por los más recientes primero y paginamos de 10 en 10
            ->latest('id')
            ->paginate(10) 
            ->withQueryString(); // Esto mantiene la palabra buscada al cambiar de página

        return view('socios.index', compact('socios'));
    }
    // Mostrar formulario para crear socio
    public function create()
    {
        // 1. Generar el próximo Folio (Ej. MX-9482)
        $ultimoSocio = Member::latest('id')->first();
        $siguienteId = $ultimoSocio ? $ultimoSocio->id + 1 : 1;
        $folio = 'MX-' . str_pad($siguienteId + 9000, 4, '0', STR_PAD_LEFT); 

        // 2. Traer solo los planes que están activos en el sistema
        $planes = \App\Models\Plan::where('is_active', true)->get();

        // 3. Fecha de hoy para mostrar en el form
        $fechaRegistro = now()->format('d/m/Y');

        return view('socios.create', compact('folio', 'fechaRegistro', 'planes'));
    }
    // Guardar el nuevo socio en la base de datos
    public function store(Request $request)
{
    // 1. Validar los datos
    $request->validate([
        'name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'email' => 'nullable|email|max:255|unique:members,email',
        'plan_id' => 'required|exists:plans,id',
        'photo' => 'nullable|mimes:jpeg,png,jpg,webp|max:5120',
    ]);

    // 2. Generar el Folio de forma segura en el backend
    $ultimoSocio = \App\Models\Member::latest('id')->first();
    $siguienteId = $ultimoSocio ? $ultimoSocio->id + 1 : 1;
    $folio = 'MX-' . str_pad($siguienteId + 9000, 4, '0', STR_PAD_LEFT);

    // 3. Obtener el plan y calcular la fecha de vencimiento y precio
    $plan = \App\Models\Plan::find($request->plan_id);
    $fechaVencimiento = \Carbon\Carbon::today()->addDays($plan->duration_days);

    // 4. Guardar la fotografía si existe
    $rutaImagen = null;
    if ($request->hasFile('photo')) {
        $nombreArchivo = uniqid('socio_') . '.webp';
        $rutaImagen = $request->file('photo')->storeAs('socios', $nombreArchivo, 'public');
    }

    // 5. Guardar al Socio en la Base de Datos
    $socio = \App\Models\Member::create([
        'folio' => $folio,
        'name' => $request->name,
        'last_name' => $request->last_name,
        'phone' => $request->phone,
        'email' => $request->email,
        'plan_id' => $plan->id,
        'expiration_date' => $fechaVencimiento,
        'profile_photo_path' => $rutaImagen,
        'is_active' => true,
    ]);

   
    // 6. Registrar el pago/ingreso incluyendo el folio de pago requerido
    \App\Models\Payment::create([
        'member_id' => $socio->id,
        'plan_id' => $plan->id,
        'plan_name' => $plan->name,
        'folio_pago' => 'P-' . str_pad($socio->id, 5, '0', STR_PAD_LEFT), // <--- Genera un folio único para el pago
        'amount' => $plan->price,
        'payment_method' => 'Efectivo', 
    ]);

    // 7. Redirigir al index con mensaje de éxito
    return redirect()->route('socios.index')->with('success', '¡Socio registrado y pago agregado a finanzas exitosamente!');
}
    // Mostrar el perfil y gafete del socio
    public function show(\App\Models\Member $member)
    {
        $member->load('plan');
        $planes = \App\Models\Plan::where('is_active', true)->get();

        $hoy = \Carbon\Carbon::today();
        $diasRestantes = 0;
        $porcentajeProgreso = 0;
        $fechaInicio = null;

        if ($member->plan && $member->expiration_date) {
            if ($member->expiration_date->isFuture()) {
                $diasRestantes = $hoy->diffInDays($member->expiration_date);
                
                // LA MAGIA: Si tiene más días acumulados que el plan original, ajustamos el ciclo
                $totalCiclo = max($member->plan->duration_days, $diasRestantes);
                
                // La fecha de inicio retrocede exactamente los días del ciclo total (evita irse al futuro)
                $fechaInicio = $member->expiration_date->copy()->subDays($totalCiclo);
                
                // Días consumidos de este nuevo ciclo
                $diasPasados = $totalCiclo - $diasRestantes;
                
                // Evitamos divisiones por cero
                if ($totalCiclo > 0) {
                    $porcentajeProgreso = ($diasPasados / $totalCiclo) * 100;
                }
            } else {
                // Si el plan ya está vencido
                $fechaInicio = $member->expiration_date->copy()->subDays($member->plan->duration_days);
                $porcentajeProgreso = 100; // Barra completamente llena
            }
        }
        // AGREGAR ESTO: Traer el historial de pagos del más reciente al más antiguo
        $pagos = \App\Models\Payment::where('member_id', $member->id)->latest()->get();

        $ultimosAccesos = \App\Models\AccessLog::where('member_id', $member->id)->latest()->take(5)->get();

        return view('socios.show', compact('member', 'diasRestantes', 'porcentajeProgreso', 'fechaInicio', 'planes', 'pagos', 'ultimosAccesos'));  
        }
            // Procesar la renovación del plan
    // Procesar la renovación del plan
    public function renew(Request $request, \App\Models\Member $member)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'payment_method' => 'required|in:efectivo,tarjeta',
        ]);

        $plan = \App\Models\Plan::find($request->plan_id);
        $hoy = \Carbon\Carbon::today();

        $fechaBase = ($member->expiration_date && $member->expiration_date->isFuture()) 
                    ? $member->expiration_date 
                    : $hoy;

        $nuevaVigencia = $fechaBase->copy()->addDays($plan->duration_days);

        // 1. Actualizar la membresía del socio
        $member->update([
            'plan_id' => $plan->id,
            'expiration_date' => $nuevaVigencia,
            'is_active' => true,
        ]);

        $folioPago = 'TXN-' . strtoupper(uniqid());

        // 2. ¡LA CLAVE! Registrar el pago en la tabla payments para que alimente Reportes y Flujo de Caja
        \App\Models\Payment::create([
            'member_id' => $member->id,
            'plan_id' => $plan->id,
            'plan_name' => $plan->name,
            'folio_pago' => $folioPago,
            'amount' => $plan->price,
            'payment_method' => ucfirst($request->payment_method), // Guarda 'Efectivo' o 'Tarjeta' con la primera letra mayúscula
        ]);

        // 3. Construir la URL del ticket
        $urlTicket = route('socios.ticket', [
            $member->id,                        
            'plan_name'  => $plan->name,            
            'amount'     => $plan->price,           
            'method'     => $request->payment_method, 
            'folio_pago' => $folioPago
        ]);

        // 4. Redirigir al perfil del socio con éxito e impresión del ticket
        return redirect()->route('socios.show', $member->id)
                         ->with('success', 'Pago registrado, plan renovado y agregado a finanzas exitosamente.')
                         ->with('imprimir_ticket', $urlTicket);
    }
    public function ticket(Request $request, \App\Models\Member $member)
    {
        // Recogemos los datos que mandamos por la URL
        $data = $request->only(['plan_name', 'amount', 'method', 'folio_pago']);
        $fecha = now()->format('d/m/Y H:i:s');
        
        return view('socios.ticket', compact('member', 'data', 'fecha'));
    }
    // Vista completa del historial de pagos
    public function payments(\App\Models\Member $member)
    {
        $pagos = \App\Models\Payment::where('member_id', $member->id)->latest()->paginate(15);
        return view('socios.pagos', compact('member', 'pagos'));
    }

    public function accesses(\App\Models\Member $member)
    {
        // Se asume que el modelo se llama AccessLog. Ajusta el nombre si en tu proyecto se llama diferente.
        $accesos = \App\Models\AccessLog::where('member_id', $member->id)->latest()->paginate(15);
        return view('socios.accesos', compact('member', 'accesos'));
    }

    // =========================================================
    // MOSTRAR FORMULARIO DE EDICIÓN
    // =========================================================
    public function edit($id)
    {
        // Buscamos al socio
        $member = \App\Models\Member::findOrFail($id);
        
        // Traemos los planes activos por si en la edición quieres cambiarle el plan
        $planes = \App\Models\Plan::where('is_active', true)->get();

        // Retornamos la vista de editar (asegúrate de tener este archivo creado)
        return view('socios.edit', compact('member', 'planes'));
    }

    // =========================================================
    // GUARDAR LOS CAMBIOS DEL SOCIO
    // =========================================================
    public function update(\Illuminate\Http\Request $request, $id)
    {
        // Buscamos al socio
        $member = \App\Models\Member::findOrFail($id);

        // Validamos que por lo menos el nombre y apellido vengan llenos
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            // Si tienes más campos obligatorios, puedes agregarlos aquí
        ]);

        // Actualizamos todos los datos que vengan en el formulario
        $member->update($request->all());

        // Regresamos a la lista con un mensaje de éxito
        return redirect()->route('socios.index')->with('success', 'Los datos del socio se actualizaron correctamente.');
    }
    // =========================================================
    // ELIMINAR SOCIO (CON PROTECCIÓN FINANCIERA)
    // =========================================================
    public function destroy($id)
    {
        try {
            // Buscamos al socio en la base de datos
            $member = \App\Models\Member::findOrFail($id);
            
            // Intentamos eliminarlo
            $member->delete();

            // Si se borra bien, regresamos con éxito
            return redirect()->route('socios.index')->with('success', 'El socio fue eliminado correctamente del sistema.');

        } catch (\Illuminate\Database\QueryException $e) {
            // SEGURIDAD: Si el socio ya tiene pagos o accesos registrados en la BD, MySQL bloqueará el borrado.
            // Lo atrapamos aquí para que no salga una pantalla de error como la que te salió.
            return redirect()->route('socios.index')->with('error', '🛡️ ¡ALERTA DE SEGURIDAD! No se puede eliminar a este socio porque ya tiene historial de pagos o accesos. Por seguridad, edita su perfil y márcalo como "Inactivo".');
            
        } catch (\Exception $e) {
            // Cualquier otro error raro
            return redirect()->route('socios.index')->with('error', 'Ocurrió un error inesperado al intentar eliminar al socio.');
        }
    }
    
}
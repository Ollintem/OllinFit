<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        // 1. Atrapamos los filtros enviados por la vista
        $busqueda = $request->input('search');
        $estado = $request->input('estado');
        $planId = $request->input('plan_id');

        // 2. Hacemos la consulta inteligente con los filtros aplicados
        $socios = Member::with('plan')
            ->when($busqueda, function ($query, $busqueda) {
                // Agrupamos la búsqueda en un closure para que no rompa los demás filtros
                return $query->where(function($q) use ($busqueda) {
                    $q->where('name', 'LIKE', "%{$busqueda}%")
                      ->orWhere('last_name', 'LIKE', "%{$busqueda}%")
                      ->orWhere('folio', 'LIKE', "%{$busqueda}%");
                });
            })
            ->when($estado, function ($query, $estado) {
                $hoy = now()->startOfDay();
                if ($estado === 'activos') {
                    return $query->where('is_active', true)->whereDate('expiration_date', '>=', $hoy);
                } elseif ($estado === 'vencidos') {
                    return $query->where('is_active', true)->whereDate('expiration_date', '<', $hoy);
                } elseif ($estado === 'inactivos') {
                    return $query->where('is_active', false);
                }
            })
            ->when($planId, function ($query, $planId) {
                return $query->where('plan_id', $planId);
            })
            ->latest('id')
            ->paginate(10) 
            ->withQueryString();

        // 3. Calculamos los KPIs (Se mantiene igual)
        $hoy = now();
        $totalActivos = Member::where('is_active', true)->whereDate('expiration_date', '>=', $hoy)->count();
        $totalVencidos = Member::where('is_active', true)->whereDate('expiration_date', '<', $hoy)->count();
        $totalPorVencer = Member::where('is_active', true)
                                ->whereDate('expiration_date', '>=', $hoy)
                                ->whereDate('expiration_date', '<=', $hoy->copy()->addDays(5))
                                ->count();

        // 4. NUEVO: Traemos todos los planes para llenar el select del filtro
        $planes = \App\Models\Plan::all();

        return view('socios.index', compact('socios', 'totalActivos', 'totalVencidos', 'totalPorVencer', 'planes'));
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
            'payment_method' => 'required|in:efectivo,tarjeta',
            'amount_received' => 'nullable|numeric|min:0', // El efectivo recibido
        ]);

        $plan = \App\Models\Plan::find($request->plan_id);

        // 🚨 CANDADO DE SEGURIDAD FINANCIERA 🚨
        // Si el método es efectivo, el cajero DEBE registrar cuánto dinero físico recibió, y debe alcanzar.
        if (strtolower($request->payment_method) === 'efectivo') {
            $recibido = (float) $request->amount_received;
            $precio = (float) $plan->price;

            if ($recibido < $precio) {
                return back()->with('error', 'El efectivo recibido ($' . number_format($recibido, 2) . ') no es suficiente para cubrir el costo del plan ($' . number_format($precio, 2) . ').');
            }
        }

        // 2. Generar el Folio de forma segura en el backend
        $ultimoSocio = \App\Models\Member::latest('id')->first();
        $siguienteId = $ultimoSocio ? $ultimoSocio->id + 1 : 1;
        $folio = 'MX-' . str_pad($siguienteId + 9000, 4, '0', STR_PAD_LEFT);

        // 3. Calcular la fecha de vencimiento
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

        // 6. Registrar el pago de la membresía en 'payments' (única fuente del ingreso por membresías).
        // Nota: NO se crea una venta en 'sales'; esa tabla es solo para ventas del POS, para no duplicar el ingreso.
        $folioPago = 'TXN-' . strtoupper(uniqid());
        \App\Models\Payment::create([
            'member_id' => $socio->id,
            'plan_id' => $plan->id,
            'plan_name' => $plan->name,
            'folio_pago' => $folioPago,
            'amount' => $plan->price,
            'payment_method' => ucfirst($request->payment_method),
        ]);

        // Ticket de membresía (se arma con los datos del pago, no requiere una fila en 'sales').
        $ticketUrl = route('socios.ticket', [
            $socio->id,
            'plan_name'  => $plan->name,
            'amount'     => $plan->price,
            'method'     => $request->payment_method,
            'folio_pago' => $folioPago,
        ]);

        // 7. Redirigir al index con mensaje y activar la impresión automática del ticket
        return redirect()->route('socios.index')->with([
            'success' => '¡Socio registrado y pago de $'. number_format($plan->price, 2) .' agregado a la caja exitosamente!',
            'imprimir_ticket' => $ticketUrl
        ]);
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
        $member = \App\Models\Member::findOrFail($id);

        $request->validate([
            'name'      => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone'     => 'nullable|string|max:20',
            'email'     => 'nullable|email|max:255',
            'photo'     => 'nullable|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $data = $request->only(['name', 'last_name', 'phone', 'email']);

        // Si se capturó una nueva fotografía en la vista
        if ($request->hasFile('photo')) {
            // Borramos la foto anterior si es que tenía una
            if ($member->profile_photo_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($member->profile_photo_path);
            }
            
            // Guardamos la nueva foto
            $nombreArchivo = uniqid('socio_') . '.webp';
            $data['profile_photo_path'] = $request->file('photo')->storeAs('socios', $nombreArchivo, 'public');
        }

        $member->update($data);

        return redirect()->route('socios.index')->with('success', 'Los datos y la fotografía del socio se actualizaron correctamente.');
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
    // =========================================================
    // MÓDULO: RENOVACIÓN DE MEMBRESÍAS (MODAL)
    // =========================================================
    
    // Función 1: Manda los datos del socio y los planes disponibles al Modal en formato JSON
    public function obtenerDatosRenovacion($id)
    {
        $socio = \App\Models\Member::findOrFail($id);
        
        $planes = \App\Models\Plan::all()->map(function ($plan) {
            return [
                'id' => $plan->id,
                'name' => $plan->name,
                'price' => (float)$plan->price,
                // Semestral da 180 días, los demás 30 días
                'days' => $plan->name === 'Semestral' ? 180 : 30, 
                'ahorro' => $plan->name === 'Semestral' ? 'AHORRO DEL 16% VS MENSUAL' : null
            ];
        });

        $fechaActual = $socio->expiration_date ? \Carbon\Carbon::parse($socio->expiration_date) : now();
        // Si ya venció, le contamos a partir de hoy
        if ($fechaActual->isPast()) {
            $fechaActual = now();
        }

        return response()->json([
            'id' => $socio->id,
            'nombre' => "{$socio->name} {$socio->last_name}",
            'folio' => $socio->folio,
            'plan_actual_id' => $socio->plan_id,
            'fecha_base' => $fechaActual->format('Y-m-d'),
            'planes' => $planes
        ]);
    }

// Función 2: Procesa el pago, actualiza la vigencia y registra en la caja
    public function renovarMensualidad(\Illuminate\Http\Request $request, $id)
    {
        // 1. Validar la entrada
        $request->validate([
            'payment_method' => 'required|in:efectivo,tarjeta',
            'amount_received' => 'nullable|numeric|min:0', // El efectivo recibido
        ]);

        $socio = \App\Models\Member::findOrFail($id);

        if ($request->filled('plan_id')) {
            $socio->plan_id = $request->plan_id;
        }

        $socio->load('plan');
        $precio = $socio->plan ? (float) $socio->plan->price : 0;

        // 🚨 CANDADO DE SEGURIDAD FINANCIERA 🚨
        if (strtolower($request->payment_method) === 'efectivo' && $precio > 0) {
            $recibido = (float) $request->amount_received;

            if ($recibido < $precio) {
                return back()->with('error', 'Cobro rechazado. El efectivo recibido ($' . number_format($recibido, 2) . ') es menor al costo de la renovación ($' . number_format($precio, 2) . ').');
            }
        }

        // Calcular nueva fecha
        $fechaActual = $socio->expiration_date ? \Carbon\Carbon::parse($socio->expiration_date) : now();
        if ($fechaActual->isPast()) {
            $fechaActual = now();
        }

        if ($socio->plan && $socio->plan->name === 'Semestral') {
            $socio->expiration_date = $fechaActual->addMonths(6);
        } else {
            // Usa los días del plan si están disponibles, si no, usa addMonth()
            if(isset($socio->plan->duration_days)){
                $socio->expiration_date = $fechaActual->addDays($socio->plan->duration_days);
            } else {
                $socio->expiration_date = $fechaActual->addMonth();
            }
        }

        $socio->is_active = true;
        $socio->save();

        $ticketUrl = null;

        // Registrar la entrada de dinero en 'payments' (única fuente del ingreso por membresías).
        // No se crea venta en 'sales' para no duplicar el ingreso en caja/reportes.
        if ($precio > 0) {
            $folioPago = 'TXN-' . strtoupper(uniqid());
            \App\Models\Payment::create([
                'member_id' => $socio->id,
                'plan_id' => $socio->plan->id,
                'plan_name' => $socio->plan->name,
                'folio_pago' => $folioPago,
                'amount' => $precio,
                'payment_method' => ucfirst($request->payment_method),
            ]);

            // Ticket de membresía, armado con los datos del pago.
            $ticketUrl = route('socios.ticket', [
                $socio->id,
                'plan_name'  => $socio->plan->name,
                'amount'     => $precio,
                'method'     => $request->payment_method,
                'folio_pago' => $folioPago,
            ]);
        }

        // Retornamos a la vista activando el disparador de impresión
        return redirect()->back()->with([
            'success' => '¡Suscripción renovada y cobro de $'. number_format($precio, 2) .' registrado con éxito!',
            'imprimir_ticket' => $ticketUrl
        ]);
    }
}
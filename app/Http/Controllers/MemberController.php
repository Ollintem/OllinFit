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
        // 1. Validar los datos (Agregamos 'webp' a los formatos permitidos)
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255|unique:members,email',
            'plan_id' => 'required|exists:plans,id',
            'photo' => 'nullable|mimes:jpeg,png,jpg,webp|max:5120', // Permitimos WebP
        ]);

        // 2. Generar el Folio de forma segura en el backend
        $ultimoSocio = \App\Models\Member::latest('id')->first();
        $siguienteId = $ultimoSocio ? $ultimoSocio->id + 1 : 1;
        $folio = 'MX-' . str_pad($siguienteId + 9000, 4, '0', STR_PAD_LEFT);

        // 3. Calcular la fecha de vencimiento según el plan seleccionado
        $plan = \App\Models\Plan::find($request->plan_id);
        $fechaVencimiento = \Carbon\Carbon::today()->addDays($plan->duration_days);

       // 4. Guardado nativo (Sin Intervention Image ni GD)
        $rutaImagen = null;
        if ($request->hasFile('photo')) {
            $nombreArchivo = uniqid('socio_') . '.webp';
            
            // Laravel guarda el archivo directamente en storage/app/public/socios
            $rutaImagen = $request->file('photo')->storeAs('socios', $nombreArchivo, 'public');
        }
        // 5. Guardar en la Base de Datos
        \App\Models\Member::create([
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

        // 6. Redirigir al index con mensaje de éxito
        return redirect()->route('socios.index')->with('success', 'Socio registrado y cobrado exitosamente.');
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
    public function renew(Request $request, \App\Models\Member $member)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'payment_method' => 'required|in:efectivo,tarjeta',
            // En la vida real, aquí conectaríamos con Stripe, Conekta o MercadoPago
        ]);

        $plan = \App\Models\Plan::find($request->plan_id);
        $hoy = \Carbon\Carbon::today();

        $fechaBase = ($member->expiration_date && $member->expiration_date->isFuture()) 
                        ? $member->expiration_date 
                        : $hoy;

        $nuevaVigencia = $fechaBase->copy()->addDays($plan->duration_days);

        $member->update([
            'plan_id' => $plan->id,
            'expiration_date' => $nuevaVigencia,
            'is_active' => true,
        ]);

        // ... (código de actualización del member)

        $folioPago = 'TXN-' . strtoupper(uniqid());

        // Guardar el historial financiero
        \App\Models\Payment::create([
            'member_id' => $member->id,
            'plan_name' => $plan->name,
            'amount' => $plan->price,
            'payment_method' => $request->payment_method,
            'folio_pago' => $folioPago,
        ]);

        // Redirigir a la vista de generación del Ticket
        return redirect()->route('socios.ticket', [
            'member' => $member->id,
            'plan_name' => $plan->name,
            'amount' => $plan->price,
            'method' => $request->payment_method,
            'folio_pago' => $folioPago // Usamos el folio real guardado
        ]);
    }
    // Mostrar la vista del ticket para imprimir
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
    
}
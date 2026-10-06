<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\AccessLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AccessController extends Controller
{
    // Mostrar la vista de la cámara y los socios adentro
    public function index()
    {
        $sociosAdentro = Member::where('is_inside', true)->latest('updated_at')->get();
        return view('acceso.index', compact('sociosAdentro'));
    }

    // Procesar el escaneo del QR vía AJAX (Sin recargar página)
    public function scan(Request $request)
    {
        $request->validate(['folio' => 'required|string']);

        $member = Member::where('folio', $request->folio)->first();

        if (!$member) {
            return response()->json(['success' => false, 'message' => 'Código no válido. Socio no encontrado.'], 404);
        }

        // 1. Validar que la membresía esté vigente
        $hoy = Carbon::today();
        if (!$member->expiration_date || $member->expiration_date->isPast()) {
            
            // Registramos el intento fallido
            AccessLog::create([
                'member_id' => $member->id,
                'access_method' => 'Escáner QR',
                'status' => 'Denegado'
            ]);

            return response()->json([
                'success' => false, 
                'message' => 'Membresía Vencida. Su plan finalizó el ' . $member->expiration_date->format('d/m/Y')
            ], 403);
        }

        // 2. Lógica Toggle (Si está afuera entra, si está adentro sale)
        $esEntrada = !$member->is_inside;
        
        $member->update([
            'is_inside' => $esEntrada
        ]);

        // 3. Registrar en el Historial de Accesos
        // Decidimos qué palabra guardar dependiendo de si va entrando o saliendo
        $estadoAcceso = $esEntrada ? 'Entrada' : 'Salida';

        AccessLog::create([
            'member_id' => $member->id,
            'access_method' => 'Escáner QR',
            'status' => $estadoAcceso
        ]);

        $mensaje = $esEntrada 
            ? '¡Acceso Permitido! Bienvenido, ' . $member->name 
            : '¡Salida Registrada! Hasta luego, ' . $member->name;

        return response()->json([
            'success' => true,
            'message' => $mensaje,
            'is_inside' => $esEntrada,
            'member' => $member
        ]);
    }

    // Botón manual para liberar a un socio por error de escaneo
    public function release(Member $member)
    {
        $member->update(['is_inside' => false]);
        
        AccessLog::create([
            'member_id' => $member->id,
            'access_method' => 'Liberación Manual',
            'status' => 'Salida'
        ]);

        return back()->with('success', $member->name . ' ha sido liberado del sistema.');
    }
}
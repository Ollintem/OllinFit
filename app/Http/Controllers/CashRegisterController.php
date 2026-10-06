<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashRegister;
use App\Models\Sale;
use App\Models\Payment;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CashRegisterController extends Controller
{
    public function index()
{
    // Buscar si hay una caja abierta actualmente
    $cajaActiva = CashRegister::where('status', 'abierta')->latest()->first();
    
    $ingresosEfectivoTurno = 0;
    $ingresosTarjetaTurno = 0;
    $ventasTurnoCount = 0;
    $detalleTransacciones = collect();

    if ($cajaActiva) {
        // Calcular ingresos desde que se abrió la caja hasta ahora
        $desde = $cajaActiva->opened_at;
        
        // 1. Ventas POS (Efectivo y Tarjeta)
        $ventasPos = Sale::where('created_at', '>=', $desde)
            ->get()
            ->map(function($item) {
                $item->origen = 'Punto de Venta';
                $item->monto = $item->total;
                $item->concepto_id = 'Ticket #' . $item->id;
                return $item;
            });

        // 2. Pagos de membresías (Efectivo y Tarjeta)
        $pagosMembresias = Payment::where('created_at', '>=', $desde)
            ->get()
            ->map(function($item) {
                $item->origen = 'Membresía / Plan';
                $item->monto = $item->amount ?? $item->total ?? 0;
                $item->concepto_id = 'Pago #' . $item->id;
                return $item;
            });

        // 3. Inscripciones de socios
        $sociosNuevos = Member::with('plan')
            ->where('created_at', '>=', $desde)
            ->get()
            ->map(function($socio) {
                $item = new \stdClass();
                $item->origen = 'Inscripción Socio';
                $item->id = $socio->id;
                $item->created_at = $socio->created_at;
                $item->payment_method = 'Efectivo'; // O el método que maneje por defecto la inscripción
                $item->monto = $socio->plan->price ?? 0;
                $item->concepto_id = $socio->folio . ' (' . $socio->name . ')';
                return $item;
            });

        // Combinar todas las transacciones del turno
        $detalleTransacciones = $ventasPos->concat($pagosMembresias)->concat($sociosNuevos)->sortByDesc('created_at');
        
        // Calcular totales separados por método de pago
        $ingresosEfectivoTurno = $detalleTransacciones->where('payment_method', 'Efectivo')->sum('monto');
        $ingresosTarjetaTurno = $detalleTransacciones->where('payment_method', 'Tarjeta')->sum('monto');
        
        $ventasTurnoCount = $detalleTransacciones->count();
    }

    return view('caja.index', compact('cajaActiva', 'ingresosEfectivoTurno', 'ingresosTarjetaTurno', 'ventasTurnoCount', 'detalleTransacciones'));
}
    public function open(Request $request)
    {
        $request->validate([
            'opening_amount' => 'required|numeric|min:0',
        ]);

        // Verificar que no haya otra caja abierta
        $abierta = CashRegister::where('status', 'abierta')->first();
        if ($abierta) {
            return back()->with('error', 'Ya existe una caja abierta en este momento.');
        }

        CashRegister::create([
            'user_id' => Auth::id(),
            'opening_amount' => $request->opening_amount,
            'status' => 'abierta',
            'opened_at' => Carbon::now(),
        ]);

        return redirect()->route('caja.index')->with('success', '¡Caja abierta exitosamente con fondo inicial!');
    }

    public function close(Request $request)
    {
        $request->validate([
            'closing_amount' => 'required|numeric|min:0',
        ]);

        $cajaActiva = CashRegister::where('status', 'abierta')->first();
        if (!$cajaActiva) {
            return back()->with('error', 'No hay ninguna caja abierta para cerrar.');
        }

        // Calcular ingresos en efectivo acumulados durante el turno
        $desde = $cajaActiva->opened_at;
        $ventasPos = Sale::where('payment_method', 'Efectivo')->where('created_at', '>=', $desde)->sum('total');
        $pagosMembresias = Payment::where('payment_method', 'Efectivo')->where('created_at', '>=', $desde)->sum('amount');
        
        $sociosNuevosIds = Member::where('created_at', '>=', $desde)->pluck('plan_id');
        $montoSocios = \App\Models\Plan::whereIn('id', $sociosNuevosIds)->sum('price') ?? 0; // Aproximación segura o cálculo de planes

        $ingresosEfectivoTurno = $ventasPos + $pagosMembresias + $montoSocios;
        $expectedAmount = $cajaActiva->opening_amount + $ingresosEfectivoTurno;
        
        $closingAmount = $request->closing_amount;
        $difference = $closingAmount - $expectedAmount; // Positivo = Sobrante, Negativo = Faltante

        $cajaActiva->update([
            'closing_amount' => $closingAmount,
            'expected_amount' => $expectedAmount,
            'difference' => $difference,
            'status' => 'cerrada',
            'closed_at' => Carbon::now(),
        ]);

        return redirect()->route('caja.index')->with('success', '¡Caja cerrada y corte registrado con éxito!');
    }
    public function historial()
{
    // Obtener todas las cajas cerradas paginadas o en lista para auditoría
    $historialCajas = CashRegister::where('status', 'cerrada')->latest()->paginate(10);
    return view('caja.historial', compact('historialCajas'));
}
}
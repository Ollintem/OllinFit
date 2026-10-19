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
        $cajaActiva = \App\Models\CashRegister::where('status', 'abierta')->latest()->first();
        // NUEVO: Buscamos cuál fue el último turno cerrado
        $ultimoTurno = \App\Models\CashRegister::where('status', 'cerrada')->latest()->first();

        if ($cajaActiva) {
            $desde = $cajaActiva->opened_at;

            // 1. Calcular Ingresos en EFECTIVO
            $ventasEfectivo = \App\Models\Sale::where('payment_method', 'Efectivo')->where('created_at', '>=', $desde);
            $pagosMembresiasEfectivo = \App\Models\Payment::where('payment_method', 'Efectivo')->where('created_at', '>=', $desde);
            
            $sociosNuevosIds = \App\Models\Member::where('created_at', '>=', $desde)->pluck('plan_id');
            $montoSocios = \App\Models\Plan::whereIn('id', $sociosNuevosIds)->sum('price') ?? 0;
            
            $ingresosEfectivoTurno = $ventasEfectivo->sum('total') + $pagosMembresiasEfectivo->sum('amount') + $montoSocios;

            // 2. Variables de la Vista (Conteos y Tarjetas)
            $ventasTurnoCount = \App\Models\Sale::where('created_at', '>=', $desde)->count() + \App\Models\Payment::where('created_at', '>=', $desde)->count();
            
            $ventasTarjeta = \App\Models\Sale::where('payment_method', '!=', 'Efectivo')->where('created_at', '>=', $desde)->sum('total');
            $pagosTarjeta = \App\Models\Payment::where('payment_method', '!=', 'Efectivo')->where('created_at', '>=', $desde)->sum('amount');
            $ingresosTarjetaTurno = $ventasTarjeta + $pagosTarjeta;

            // 3. EGRESOS Y GASTOS 
            $gastosTurno = \App\Models\Expense::with(['user', 'supplier'])
                            ->where('cash_register_id', $cajaActiva->id)
                            ->latest()
                            ->get();
                            
            $egresosEfectivo = $gastosTurno->where('payment_method', 'Efectivo')->sum('amount');

            // 4. Matemática en vivo de la caja
            $expectedAmount = ($cajaActiva->opening_amount + $ingresosEfectivoTurno) - $egresosEfectivo;

            // 5. RESTAURAR EL DETALLE DE TRANSACCIONES (Para la tabla de ingresos)
            $ventas = \App\Models\Sale::where('created_at', '>=', $desde)->get()->map(function($sale) {
                return (object) [
                    'created_at' => $sale->created_at,
                    'concepto_id' => 'Venta de Productos / POS',
                    'origen' => $sale->payment_method,
                    'monto' => $sale->total
                ];
            });

            $pagos = \App\Models\Payment::where('created_at', '>=', $desde)->get()->map(function($pago) {
                return (object) [
                    'created_at' => $pago->created_at,
                    'concepto_id' => 'Pago de Membresía',
                    'origen' => $pago->payment_method,
                    'monto' => $pago->amount
                ];
            });

            $detalleTransacciones = $ventas->concat($pagos)->sortByDesc('created_at');

            return view('caja.index', compact(
                'cajaActiva', 
                'ultimoTurno', // <-- Añadido aquí
                'ingresosEfectivoTurno', 
                'ventasTurnoCount', 
                'ingresosTarjetaTurno', 
                'gastosTurno', 
                'egresosEfectivo', 
                'expectedAmount',
                'detalleTransacciones' 
            ));
        }

        // NUEVO: Le pasamos la variable también cuando la caja está cerrada
        return view('caja.index', compact('cajaActiva', 'ultimoTurno'));
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
        $montoSocios = \App\Models\Plan::whereIn('id', $sociosNuevosIds)->sum('price') ?? 0;

        $ingresosEfectivoTurno = $ventasPos + $pagosMembresias + $montoSocios;

        // NUEVO: Restamos los gastos en efectivo que salieron de esta caja
        $egresosEfectivo = \App\Models\Expense::where('cash_register_id', $cajaActiva->id)
                            ->where('payment_method', 'Efectivo')
                            ->sum('amount');

        // MATEMÁTICA FINAL DE CAJA: Fondo Inicial + Ingresos - Egresos
        $expectedAmount = ($cajaActiva->opening_amount + $ingresosEfectivoTurno) - $egresosEfectivo;
        
        $closingAmount = $request->closing_amount;
        $difference = $closingAmount - $expectedAmount; // Positivo = Sobrante, Negativo = Faltante

        $cajaActiva->update([
            'closing_amount' => $closingAmount,
            'expected_amount' => $expectedAmount,
            'difference' => $difference,
            'status' => 'cerrada',
            'closed_at' => \Carbon\Carbon::now(),
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
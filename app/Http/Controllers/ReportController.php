<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Payment;
use App\Models\Member;
use App\Models\Expense; // IMPORTANTE: Agregamos el modelo de Gastos
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $fechaSeleccionada = $request->input('fecha', date('Y-m-d'));

        // ==========================================
        // 1. INGRESOS (Entradas de dinero)
        // ==========================================
        
        $ventasPos = Sale::whereDate('created_at', $fechaSeleccionada)->get()->map(function($item) {
            $item->origen = 'Punto de Venta';
            $item->monto = $item->total;
            $item->payment_method = $item->payment_method ?? 'Efectivo';
            $item->concepto_id = 'Ticket #' . $item->id;
            return $item;
        });
        
        $pagosMembresias = Payment::whereDate('created_at', $fechaSeleccionada)->get()->map(function($item) {
            $item->origen = 'Membresía / Plan';
            $item->monto = $item->amount ?? $item->total ?? 0;
            $item->payment_method = $item->payment_method ?? 'Efectivo';
            $item->concepto_id = 'Pago #' . $item->id;
            return $item;
        });

        $sociosNuevos = Member::with('plan')->whereDate('created_at', $fechaSeleccionada)->get()->map(function($socio) {
            $item = new \stdClass();
            $item->id = $socio->id;
            $item->created_at = $socio->created_at;
            $item->origen = 'Inscripción Socio';
            $item->monto = $socio->plan->price ?? 0;
            $item->payment_method = 'Efectivo';
            $item->concepto_id = $socio->folio . ' (' . $socio->name . ')';
            return $item;
        });

        $transacciones = $ventasPos->concat($pagosMembresias)->concat($sociosNuevos)->sortByDesc('created_at');
        $totalIngresos = $transacciones->sum('monto');
        $efectivoIngresos = $transacciones->where('payment_method', 'Efectivo')->sum('monto');
        $tarjetaIngresos = $transacciones->where('payment_method', 'Tarjeta')->sum('monto');

        // ==========================================
        // 2. EGRESOS (Salidas de dinero)
        // ==========================================
        $egresos = Expense::with(['supplier'])->whereDate('created_at', $fechaSeleccionada)->latest()->get();
        $totalEgresos = $egresos->sum('amount');
        
        // ==========================================
        // 3. BALANCE NETO Y EXTRAS
        // ==========================================
        $balanceNeto = $totalIngresos - $totalEgresos;

        $fechaBase = Carbon::parse($fechaSeleccionada);
        $fechaLimite = (clone $fechaBase)->addDays(5);
        $renovacionesPendientesCount = Member::whereBetween('expiration_date', [$fechaBase->toDateString(), $fechaLimite->toDateString()])
            ->where('is_active', true)
            ->count();

        $imprimir = false;

        return view('reports.index', compact(
            'transacciones', 
            'egresos',
            'totalIngresos', 
            'totalEgresos',
            'balanceNeto',
            'efectivoIngresos', 
            'tarjetaIngresos', 
            'fechaSeleccionada', 
            'renovacionesPendientesCount',
            'imprimir'
        ));
    }

    public function exportarPdf(Request $request)
    {
        $fechaSeleccionada = $request->input('fecha', date('Y-m-d'));
        // (Dejaremos esta función básica por ahora hasta que maquetemos el PDF financiero)
        return redirect()->back()->with('error', 'El formato PDF financiero está en construcción.');
    }
}
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
        $hoy = date('Y-m-d');
        $fechaSeleccionada = $request->input('fecha', $hoy);

        // Solo quien tenga 'ver_reportes_historicos' (p. ej. Administrador) puede consultar días anteriores.
        if ($fechaSeleccionada !== $hoy && ! $request->user()->can('ver_reportes_historicos')) {
            return redirect()->route('reports.index')
                ->with('error', 'Solo un administrador puede consultar reportes de días anteriores.');
        }

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

        // Nota: las inscripciones y renovaciones ya están incluidas en $pagosMembresias (tabla payments).
        // No se vuelven a sumar aquí para no duplicar el ingreso.
        $transacciones = $ventasPos->concat($pagosMembresias)->sortByDesc('created_at');
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
        $hoy = date('Y-m-d');
        $fechaSeleccionada = $request->input('fecha', $hoy);

        // Misma restricción que el reporte: días anteriores solo con permiso.
        if ($fechaSeleccionada !== $hoy && ! $request->user()->can('ver_reportes_historicos')) {
            return redirect()->route('reports.index')
                ->with('error', 'Solo un administrador puede consultar reportes de días anteriores.');
        }

        // (Dejaremos esta función básica por ahora hasta que maquetemos el PDF financiero)
        return redirect()->back()->with('error', 'El formato PDF financiero está en construcción.');
    }
}
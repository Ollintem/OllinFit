<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Payment;
use App\Models\Member;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $fechaSeleccionada = $request->input('fecha', date('Y-m-d'));

        // 1. Ventas del Punto de Venta
        $ventasPos = Sale::whereDate('created_at', $fechaSeleccionada)->get()->map(function($item) {
            $item->origen = 'Punto de Venta';
            $item->monto = $item->total;
            $item->payment_method = $item->payment_method ?? 'Efectivo';
            $item->concepto_id = 'Ticket #' . $item->id;
            return $item;
        });
        
        // 2. Pagos registrados en la tabla payments
        $pagosMembresias = Payment::whereDate('created_at', $fechaSeleccionada)->get()->map(function($item) {
            $item->origen = 'Membresía / Plan';
            $item->monto = $item->amount ?? $item->total ?? 0;
            $item->payment_method = $item->payment_method ?? 'Efectivo';
            $item->concepto_id = 'Pago #' . $item->id;
            return $item;
        });

        // 3. Socios nuevos registrados en esa fecha
        $sociosNuevos = Member::with('plan')->whereDate('created_at', $fechaSeleccionada)->get()->map(function($socio) {
            $item = new \stdClass();
            $item->id = $socio->id;
            $item->created_at = $socio->created_at;
            $item->origen = 'Inscripción Socio';
            $item->monto = $socio->plan->price ?? 0;
            $item->payment_method = 'Efectivo';
            $item->concepto_id = $socio->folio . ' (' . $socio->name . ' ' . $socio->last_name . ')';
            return $item;
        });

        $transacciones = $ventasPos->concat($pagosMembresias)->concat($sociosNuevos)->sortByDesc('created_at');

        $totalIngresos = $transacciones->sum('monto');
        $totalVentas = $transacciones->count();
        
        $efectivo = $transacciones->where('payment_method', 'Efectivo')->sum('monto');
        $tarjeta = $transacciones->where('payment_method', 'Tarjeta')->sum('monto');

        $fechaBase = Carbon::parse($fechaSeleccionada);
        $fechaLimite = (clone $fechaBase)->addDays(5);
        
        $renovacionesPendientesCount = Member::whereBetween('expiration_date', [$fechaBase->toDateString(), $fechaLimite->toDateString()])
            ->where('is_active', true)
            ->count();

        $imprimir = false;

        return view('reports.index', compact(
            'transacciones', 
            'totalIngresos', 
            'totalVentas', 
            'efectivo', 
            'tarjeta', 
            'fechaSeleccionada', 
            'renovacionesPendientesCount',
            'imprimir'
        ));
    }

    public function exportarPdf(Request $request)
    {
        $fechaSeleccionada = $request->input('fecha', date('Y-m-d'));
        
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
            $item->concepto_id = $socio->folio . ' (' . $socio->name . ' ' . $socio->last_name . ')';
            return $item;
        });

        $transacciones = $ventasPos->concat($pagosMembresias)->concat($sociosNuevos)->sortByDesc('created_at');
        $totalIngresos = $transacciones->sum('monto');
        $efectivo = $transacciones->where('payment_method', 'Efectivo')->sum('monto');
        $tarjeta = $transacciones->where('payment_method', 'Tarjeta')->sum('monto');

        // AQUÍ ESTABA EL DETALLE: Debe cargar 'reports.pdf' y no 'reports.index'
        return view('reports.pdf', compact('transacciones', 'totalIngresos', 'efectivo', 'tarjeta', 'fechaSeleccionada'));
    }
}
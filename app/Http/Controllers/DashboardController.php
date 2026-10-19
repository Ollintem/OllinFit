<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale; 
use App\Models\Payment; 
use App\Models\Member; 
use App\Models\AccessLog; 
use App\Models\SaleItem;
use App\Models\Expense; 
use App\Models\CashRegister; 
use App\Models\Product; 
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        // 1. ESTADO DE CAJA EN VIVO
        $cajaAbierta = CashRegister::where('status', 'abierta')->latest()->first();
        $estadoCaja = $cajaAbierta ? 'ABIERTA' : 'CERRADA';
        $dineroEnCaja = 0;

        if ($cajaAbierta) {
            $ventasCaja = Sale::where('created_at', '>=', $cajaAbierta->created_at)->where('payment_method', 'Efectivo')->sum('total');
            $membresiasCaja = Payment::where('created_at', '>=', $cajaAbierta->created_at)->where('payment_method', 'Efectivo')->sum('amount') ?? 0;
            $gastosCaja = Expense::where('created_at', '>=', $cajaAbierta->created_at)->where('payment_method', 'Efectivo')->sum('amount') ?? 0;
            $dineroEnCaja = ($cajaAbierta->initial_amount ?? 0) + $ventasCaja + $membresiasCaja - $gastosCaja;
        }

        // 2. UTILIDAD Y CRECIMIENTO
        $ventasTienda = Sale::whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->sum('total');
        $ventasMembresias = Payment::whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->sum('amount') ?? 0;
        $ingresosMes = $ventasTienda + $ventasMembresias;
        $gastosMes = Expense::whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->sum('amount');
        $utilidadMes = $ingresosMes - $gastosMes;

        $ingresosMesAnt = Sale::whereMonth('created_at', $lastMonth->month)->whereYear('created_at', $lastMonth->year)->sum('total') + 
                          (Payment::whereMonth('created_at', $lastMonth->month)->whereYear('created_at', $lastMonth->year)->sum('amount') ?? 0);
        $gastosMesAnt = Expense::whereMonth('created_at', $lastMonth->month)->whereYear('created_at', $lastMonth->year)->sum('amount');
        $utilidadMesAnt = $ingresosMesAnt - $gastosMesAnt;
        $crecimientoUtilidad = $utilidadMesAnt > 0 ? (($utilidadMes - $utilidadMesAnt) / $utilidadMesAnt) * 100 : 0;

        // 3. DATOS PARA GRÁFICAS FILTRABLES
        $ventasAnio = []; $labelsAnio = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        for ($i = 1; $i <= 12; $i++) {
            $ventasAnio[] = Sale::whereYear('created_at', $now->year)->whereMonth('created_at', $i)->sum('total') + (Payment::whereYear('created_at', $now->year)->whereMonth('created_at', $i)->sum('amount') ?? 0);
        }

        $ventasMes = []; $labelsMes = [];
        $daysInMonth = $now->daysInMonth;
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $ventasMes[] = Sale::whereYear('created_at', $now->year)->whereMonth('created_at', $now->month)->whereDay('created_at', $i)->sum('total') + (Payment::whereYear('created_at', $now->year)->whereMonth('created_at', $now->month)->whereDay('created_at', $i)->sum('amount') ?? 0);
            $labelsMes[] = 'Día ' . $i; // <-- SOLUCIÓN 1: Formato más intuitivo para el mes
        }

        $ventasSemana = []; $labelsSemana = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
        $startOfWeek = $now->copy()->startOfWeek();
        for ($i = 0; $i < 7; $i++) {
            $currentDay = $startOfWeek->copy()->addDays($i);
            $ventasSemana[] = Sale::whereDate('created_at', $currentDay->toDateString())->sum('total') + (Payment::whereDate('created_at', $currentDay->toDateString())->sum('amount') ?? 0);
        }

        $graficasDataJson = json_encode(['anio' => ['labels' => $labelsAnio, 'data' => $ventasAnio], 'mes' => ['labels' => $labelsMes, 'data' => $ventasMes], 'semana' => ['labels' => $labelsSemana, 'data' => $ventasSemana]]);
        $distribucionIngresosJson = json_encode([$ventasTienda, $ventasMembresias]);

        // 4. SOLUCIÓN AL TICKET: Extraemos los datos completos del ticket directamente aquí
        $ultimosPagos = Sale::latest()->take(5)->get()->map(function($sale) {
            $sale->ticket_date = $sale->created_at->format('d/m/Y h:i A');
            $sale->ticket_items = SaleItem::join('products', 'sale_items.product_id', '=', 'products.id')
                ->where('sale_items.sale_id', $sale->id)
                ->select('products.name', 'sale_items.quantity', 'sale_items.price')
                ->get();
            return $sale;
        });

        // 5. SOLUCIÓN AL STOCK: Filtramos para que no cuente los que ya están en 0 (Agotados)
        $sociosActivos = Member::count();
        $accesosHoy = AccessLog::whereDate('created_at', Carbon::today())->count();
        $accesosAyer = AccessLog::whereDate('created_at', Carbon::yesterday())->count();
        $crecimientoAccesos = $accesosAyer > 0 ? (($accesosHoy - $accesosAyer) / $accesosAyer) * 100 : 0;
        $sociosPorVencer = Member::whereBetween('expiration_date', [$now, $now->copy()->addDays(5)])->count();
        
        // Filtra artículos entre 1 y 5 piezas. Si quieres contar los de 0, cambia el > 0 a >= 0
        $productosBajos = Product::where('stock', '<=', 5)->where('stock', '>', 0)->count();

        $topProductos = SaleItem::join('products', 'sale_items.product_id', '=', 'products.id')
            ->select('products.name as nombre', DB::raw('SUM(sale_items.quantity) as total_vendido'))
            ->whereMonth('sale_items.created_at', $now->month)->whereYear('sale_items.created_at', $now->year)
            ->groupBy('sale_items.product_id', 'products.name')->orderByDesc('total_vendido')->take(5)->get();

        $totalPiezasTop = $topProductos->sum('total_vendido');
        $productoMasVendido = $topProductos->first()->nombre ?? 'N/A';

        return view('dashboard', compact(
            'estadoCaja', 'dineroEnCaja', 'utilidadMes', 'crecimientoUtilidad',
            'graficasDataJson', 'distribucionIngresosJson', 'ventasTienda', 'ventasMembresias', 
            'ultimosPagos', 'sociosActivos', 'accesosHoy', 'crecimientoAccesos',
            'sociosPorVencer', 'productosBajos', 'topProductos', 'totalPiezasTop', 'productoMasVendido'
        ));
    }
}
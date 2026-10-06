<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale; // O el modelo donde guardes tus ventas/pagos
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Ingresos de la semana actual
        $ingresosSemana = Sale::whereBetween('created_at', [
            Carbon::now()->startOfWeek(), 
            Carbon::now()->endOfWeek()
        ])->sum('total'); // Cambia 'total' por el campo de dinero de tu tabla

        // 2. Ingresos del mes actual
        $ingresosMes = Sale::whereMonth('created_at', Carbon::now()->month)
                             ->whereYear('created_at', Carbon::now()->year)
                             ->sum('total');

        // 3. Datos para la gráfica de barras por mes (Ene - Dic)
        $ventasPorMes = [];
        for ($i = 1; $i <= 12; $i++) {
            $ventasPorMes[$i] = Sale::whereYear('created_at', Carbon::now()->year)
                                    ->whereMonth('created_at', $i)
                                    ->sum('total');
        }

        // 4. Últimos pagos registrados
        $ultimosPagos = Sale::latest()->take(5)->get();

        return view('dashboard', compact('ingresosSemana', 'ingresosMes', 'ventasPorMes', 'ultimosPagos'));
    }
}
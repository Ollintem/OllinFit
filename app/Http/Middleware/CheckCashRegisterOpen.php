<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\CashRegister;

class CheckCashRegisterOpen
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Buscamos si existe al menos una caja con estado 'abierta'
        // (Nota: si tu sistema maneja cajas por usuario, puedes añadir: ->where('user_id', auth()->id()) )
        $cajaAbierta = CashRegister::where('status', 'abierta')->first();

        // Si no hay caja abierta, bloqueamos el paso y redirigimos
        if (!$cajaAbierta) {
            return redirect()->route('caja.index')->with('error', 'Caja cerrada. Debes abrir un nuevo turno antes de registrar cobros o ventas.');
        }

        // Si la caja está abierta, le permitimos continuar con la venta
        return $next($request);
    }
}
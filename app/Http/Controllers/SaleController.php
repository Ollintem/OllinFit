<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SaleController extends Controller
{
public function index()
{
    $productos = \App\Models\Product::all();
    return view('sales.index', compact('productos'));
}

    public function store(Request $request)
{
    $cartData = $request->input('cart_data');

    // Si llega como texto JSON, lo convertimos; si ya es arreglo, lo usamos directo
    if (is_string($cartData)) {
        $cartData = json_decode($cartData, true);
    }

    // Validamos la estructura del carrito en el servidor. No confiamos en el cliente.
    $validator = Validator::make(['cart_data' => $cartData], [
        'cart_data' => 'required|array|min:1',
        'cart_data.*.id' => 'required|integer|exists:products,id',
        'cart_data.*.quantity' => 'required|integer|min:1',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => 'Datos de la venta inválidos.', 'detalles' => $validator->errors()], 422);
    }

    // Normalizamos el método de pago a un valor permitido (por defecto Efectivo).
    $metodoPago = ucfirst(strtolower((string) $request->payment_method));
    if (! in_array($metodoPago, ['Efectivo', 'Tarjeta'], true)) {
        $metodoPago = 'Efectivo';
    }

    // Agrupamos las cantidades por producto (evita que líneas repetidas burlen el chequeo de stock).
    $cantidades = [];
    foreach ($cartData as $item) {
        $cantidades[(int) $item['id']] = ($cantidades[(int) $item['id']] ?? 0) + (int) $item['quantity'];
    }

    DB::beginTransaction();
    try {
        // Bloqueamos los productos durante la transacción y los indexamos por id.
        $productos = Product::whereIn('id', array_keys($cantidades))->lockForUpdate()->get()->keyBy('id');

        // El precio y la disponibilidad SIEMPRE se toman de la BD, nunca del carrito del cliente.
        $total = 0;
        foreach ($cantidades as $productId => $cantidad) {
            $product = $productos[$productId];
            if (! $product->is_service && $product->stock < $cantidad) {
                DB::rollBack();
                return response()->json([
                    'error' => "Stock insuficiente para \"{$product->name}\". Disponible: {$product->stock}, solicitado: {$cantidad}.",
                ], 422);
            }
            $total += $product->price * $cantidad;
        }

        $sale = Sale::create([
            'user_id' => Auth::id(),
            'total' => $total,
            'payment_method' => $metodoPago,
        ]);

        foreach ($cantidades as $productId => $cantidad) {
            $product = $productos[$productId];

            if (! $product->is_service) {
                $product->decrement('stock', $cantidad);
            }

            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $productId,
                'quantity' => $cantidad,
                'price' => $product->price, // precio real de la BD
            ]);
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'sale_id' => $sale->id
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
public function ticket($id)
    {
        $sale = \App\Models\Sale::findOrFail($id);

        $items = \App\Models\SaleItem::join('products', 'sale_items.product_id', '=', 'products.id')
                    ->where('sale_items.sale_id', $id)
                    ->select('sale_items.*', 'products.name as product_name')
                    ->get();

        return view('sales.ticket', compact('sale', 'items'));
    }
    public function getTicketData($id)
    {
        $sale = \App\Models\Sale::findOrFail($id);
        
        $items = \App\Models\SaleItem::join('products', 'sale_items.product_id', '=', 'products.id')
                    ->where('sale_items.sale_id', $id)
                    ->select('sale_items.quantity', 'sale_items.price', 'products.name')
                    ->get();

        return response()->json([
            'id' => $sale->id,
            'date' => $sale->created_at->format('d/m/Y H:i'),
            'method' => $sale->payment_method ?? 'Efectivo',
            'items' => $items,
            'total' => (float) $sale->total,
            // Si no guardas el cambio en BD, enviamos valores por defecto para que la vista no falle
            'received' => (float) ($sale->received_amount ?? $sale->total),
            'change' => (float) ($sale->change ?? 0),
        ]);
    }
    public function history(\Illuminate\Http\Request $request)
    {
        // Iniciamos la consulta base
        $query = \App\Models\Sale::query();

        // 1. Filtro por Folio Exacto
        if ($request->filled('folio')) {
            // Limpiamos por si el usuario escribe "#MX-5" o solo "5"
            $idLimpio = str_replace('#MX-', '', $request->folio);
            $query->where('id', $idLimpio);
        }

        // 2. Filtro por Método de Pago
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // 3. Filtro por Rango de Fechas (Desde - Hasta)
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Ejecutamos la consulta ordenando por los más recientes y paginamos.
        // withQueryString() mantiene los filtros aplicados al cambiar de página.
        $ventas = $query->latest()->paginate(15)->withQueryString();
        
        return view('sales.history', compact('ventas'));
    }
}
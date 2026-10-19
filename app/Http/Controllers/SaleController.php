<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    if (empty($cartData)) {
        return response()->json(['error' => 'El carrito está vacío o no es válido.'], 400);
    }

    \Illuminate\Support\Facades\DB::beginTransaction();
    try {
        $sale = \App\Models\Sale::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id() ?? 1,
            'total' => collect($cartData)->sum(fn(array $i) => $i['price'] * $i['quantity']),
            'payment_method' => $request->payment_method ?? 'Efectivo',
        ]);

        foreach ($cartData as $item) {
            $product = \App\Models\Product::find($item['id']);
            if ($product && !$product->is_service) {
                $product->stock -= $item['quantity'];
                $product->save();
            }

            if (class_exists(\App\Models\SaleItem::class)) {
                \App\Models\SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }
        }

        \Illuminate\Support\Facades\DB::commit();

        return response()->json([
            'success' => true,
            'sale_id' => $sale->id
        ]);

    } catch (\Exception $e) {
        \Illuminate\Support\Facades\DB::rollBack();
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
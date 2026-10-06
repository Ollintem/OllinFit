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
            'user_id' => auth()->id() ?? 1,
            'total' => collect($cartData)->sum(fn($i) => $i['price'] * $i['quantity']),
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
}
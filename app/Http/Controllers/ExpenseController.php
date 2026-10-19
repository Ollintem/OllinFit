<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\Expense; 
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        // 1. Atrapamos los filtros enviados por la vista
        $busqueda = $request->input('search');
        $tipo = $request->input('type');
        $metodo = $request->input('payment_method');
        $proveedorId = $request->input('supplier_id');

        // 2. Consulta inteligente con filtros aplicados
        $gastos = Expense::with(['user', 'supplier'])
            ->when($busqueda, function ($query, $busqueda) {
                return $query->where(function($q) use ($busqueda) {
                    $q->where('concept', 'LIKE', "%{$busqueda}%")
                      ->orWhere('description', 'LIKE', "%{$busqueda}%")
                      ->orWhereHas('supplier', function($s) use ($busqueda) {
                          // ¡AQUÍ ESTABA EL ERROR! Cambiado a $busqueda
                          $s->where('name', 'LIKE', "%{$busqueda}%"); 
                      });
                });
            })
            ->when($tipo, function ($query, $tipo) {
                return $query->where('type', $tipo);
            })
            ->when($metodo, function ($query, $metodo) {
                return $query->where('payment_method', $metodo);
            })
            ->when($proveedorId, function ($query, $proveedorId) {
                return $query->where('supplier_id', $proveedorId);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        // 3. Traemos proveedores para llenar el filtro <select>
        $suppliers = Supplier::orderBy('name')->get();

        return view('gastos.index', compact('gastos', 'suppliers'));
    }
    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        return view('gastos.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:compra_proveedor,gasto_general',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string',
            'description' => 'required|string',
            'concept' => 'required_if:type,gasto_general',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'items' => 'nullable|array',
        ]);

        $cajaActiva = \App\Models\CashRegister::where('status', 'abierta')->latest()->first();

        if ($request->payment_method === 'Efectivo' && !$cajaActiva) {
            return back()->withInput()->with('error', 'No puedes registrar un gasto en efectivo si la caja está cerrada.');
        }

        $expense = Expense::create([
            'type' => $request->type,
            'concept' => $request->type === 'compra_proveedor' ? 'Compra de Inventario' : $request->concept,
            'description' => $request->description,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'supplier_id' => $request->type === 'compra_proveedor' ? $request->supplier_id : null,
            'cash_register_id' => $cajaActiva ? $cajaActiva->id : null,
            'user_id' => Auth::id(),
        ]);

        if ($request->type === 'compra_proveedor' && $request->filled('items_json')) {
            $cartItems = json_decode($request->items_json, true);
            
            if (is_array($cartItems)) {
                foreach ($cartItems as $item) {
                    \App\Models\ExpenseItem::create([
                        'expense_id' => $expense->id,
                        'product_id' => $item['id'],
                        'quantity' => $item['qty'],
                        'unit_cost' => $item['cost'],
                        'subtotal' => $item['qty'] * $item['cost'],
                    ]);

                    \App\Models\Product::where('id', $item['id'])->increment('stock', $item['qty']);
                }
            }
        }

        return redirect()->route('gastos.index')->with('success', 'Salida de dinero registrada y stock actualizado.');
    }

    // ==========================================
    // NUEVO: Editar Gasto (Formulario)
    // ==========================================
    public function edit($id)
    {
        $gasto = Expense::findOrFail($id);
        $suppliers = Supplier::orderBy('name')->get();
        return view('gastos.edit', compact('gasto', 'suppliers'));
    }

    // ==========================================
    // NUEVO: Actualizar Gasto
    // ==========================================
    public function update(Request $request, $id)
    {
        $gasto = Expense::findOrFail($id);
        
        $request->validate([
            'description' => 'required|string',
            'concept' => 'required_if:type,gasto_general',
        ]);

        // Nota financiera: Por seguridad, solo se permite editar textos. 
        // Si hay un error de dinero, es mejor eliminar el ticket y hacerlo de nuevo.
        $gasto->update([
            'description' => $request->description,
            'concept' => $gasto->type === 'gasto_general' ? $request->concept : 'Compra de Inventario',
        ]);

        return redirect()->route('gastos.index')->with('success', 'Los detalles del gasto se actualizaron correctamente.');
    }

    // ==========================================
    // NUEVO: Eliminar Gasto (Con rollback de inventario)
    // ==========================================
    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);

        // Si fue compra de inventario, debemos REVERTIR EL STOCK
        if ($expense->type === 'compra_proveedor') {
            $items = \App\Models\ExpenseItem::where('expense_id', $expense->id)->get();
            
            foreach ($items as $item) {
                \App\Models\Product::where('id', $item->product_id)->decrement('stock', $item->quantity);
            }
            
            // Borramos el detalle de la compra
            \App\Models\ExpenseItem::where('expense_id', $expense->id)->delete();
        }

        // Borramos el registro principal
        $expense->delete();

        return redirect()->route('gastos.index')->with('success', 'El registro ha sido eliminado y el inventario revertido correctamente.');
    }
}
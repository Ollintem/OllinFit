<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\Expense; // Importamos Gastos para el candado de seguridad

class SupplierController extends Controller
{
    public function index()
    {
        $proveedores = Supplier::orderBy('name')->get();
        return view('proveedores.index', compact('proveedores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        Supplier::create($request->all());

        return redirect()->route('proveedores.index')->with('success', 'Proveedor registrado exitosamente.');
    }

    // ==========================================
    // ACTUALIZAR PROVEEDOR
    // ==========================================
    public function update(Request $request, $id)
    {
        $proveedor = Supplier::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $proveedor->update($request->all());

        return redirect()->route('proveedores.index')->with('success', 'Los datos del proveedor se actualizaron correctamente.');
    }

    // ==========================================
    // ELIMINAR PROVEEDOR (CON CANDADO DE SEGURIDAD)
    // ==========================================
    public function destroy($id)
    {
        $proveedor = Supplier::findOrFail($id);

        // 🚨 CANDADO: Verificar si este proveedor ya tiene compras registradas en Gastos
        $tieneCompras = Expense::where('supplier_id', $id)->exists();

        if ($tieneCompras) {
            return redirect()->route('proveedores.index')->with('error', '🛡️ Seguridad Financiera: No puedes eliminar a este proveedor porque ya tienes compras e inventario registrado con su nombre. Edita su nombre si hubo algún error de captura.');
        }

        $proveedor->delete();

        return redirect()->route('proveedores.index')->with('success', 'Proveedor eliminado correctamente del catálogo.');
    }
}
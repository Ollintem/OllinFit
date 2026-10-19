<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // =========================================================
    // 1. Mostrar la lista de productos
    // =========================================================
   public function index()
{
    $productos = \App\Models\Product::all(); // O con paginación, según lo tengas
    
    return view('productos.index', compact('productos'));
}

    // =========================================================
    // 2. Mostrar el formulario para crear un producto
    // =========================================================
    public function create()
    {
        return view('productos.create');
    }

    // =========================================================
    // 3. Guardar el producto y convertir imagen a WEBP
    // =========================================================
public function store(Request $request)
{
    // Forzamos a ver qué está llegando del select
    // Si $request->tipo_item es 'acceso', asignará 1, de lo contrario 0.
    $esAcceso = ($request->input('is_service') == 1 || $request->input('type') === 'Acceso');

    $product = new Product();
    $product->name = $request->name;
    $product->price = $request->price;
   $product->is_service = $request->input('is_service', 0);
    if ($product->is_service) {
        $product->stock = 0;
        $product->min_stock = 0;
        $product->image_path = null;
    } else {
        $product->stock = $request->stock ?? 0;
        $product->min_stock = $request->min_stock ?? 5;

        if ($request->hasFile('image')) {
            $product->image_path = $this->convertirYGuardarWebp($request->file('image'), 'products');
        }
    }

    $product->save();

    return redirect()->route('productos.index')->with('success', '¡Artículo registrado correctamente!');
}
    // =========================================================
    // 4. Mostrar el formulario para editar/reabastecer
    // =========================================================
    public function edit(Product $product)
    {
        return view('productos.edit', compact('product'));
    }

    // =========================================================
    // 5. Actualizar los datos y reemplazar imagen en WEBP
    // =========================================================
 public function update(Request $request, Product $product)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|numeric',
        'tipo_item' => 'required|string|in:extra,acceso',
        'min_stock' => 'nullable|integer|min:0',
        'image' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
    ]);

    $product->name = $request->name;
    $product->price = $request->price;
    $product->min_stock = $request->min_stock ?? 5;
    
    // AQUÍ ESTÁ LA CLAVE: Leemos el menú desplegable en lugar de una casilla
    $product->is_service = ($request->tipo_item === 'acceso'); 

    if ($product->is_service) {
        $product->stock = 0; // Los pases no llevan inventario físico
        $product->image_path = null; // Los pases no llevan imagen
    } else {
        $product->stock += $request->input('stock_a_agregar', 0);

        if ($request->hasFile('image')) {
            if ($product->image_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($product->image_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image_path);
            }
            // Usamos tu función para convertir y guardar en WebP automáticamente
            $product->image_path = $this->convertirYGuardarWebp($request->file('image'), 'products');
        }
    }

    $product->save();

    return redirect()->route('productos.index')->with('success', '¡Información actualizada correctamente!');
}

    // =========================================================
    // 6. Eliminar un producto
    // =========================================================
    public function destroy(Product $product)
    {
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }
        
        $product->delete();

        return redirect()->route('productos.index')->with('success', 'Producto eliminado.');
    }


    // =========================================================
    // FUNCIÓN PRIVADA: Conversor automático a WEBP
    // =========================================================
    private function convertirYGuardarWebp($archivo, $carpeta)
    {
        // 1. Generamos un nombre único con extensión .webp
        $nombreArchivo = uniqid() . '.webp';
        
        // 2. Nos aseguramos de que la carpeta exista en public/storage
        $rutaDestino = storage_path('app/public/' . $carpeta);
        if (!file_exists($rutaDestino)) {
            mkdir($rutaDestino, 0755, true);
        }

        // 3. Cargamos la imagen original en la memoria de PHP
        $imagen = imagecreatefromstring(file_get_contents($archivo->getRealPath()));
        
        // 4. Mantenemos las transparencias por si subes un archivo PNG sin fondo
        imagealphablending($imagen, false);
        imagesavealpha($imagen, true);
        
        // 5. Guardamos la imagen convertida a WEBP con una calidad del 80% (excelente relación peso/calidad)
        $rutaCompleta = $rutaDestino . '/' . $nombreArchivo;
        imagewebp($imagen, $rutaCompleta, 80);
        
        // 6. Liberamos la memoria del servidor
        imagedestroy($imagen);

        // 7. Retornamos la ruta relativa para guardarla en la base de datos
        return $carpeta . '/' . $nombreArchivo;
    }
}
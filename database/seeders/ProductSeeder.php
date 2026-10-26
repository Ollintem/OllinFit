<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

/**
 * Catálogo de productos para probar el POS, el inventario y las alertas de
 * stock bajo del dashboard. Incluye un servicio (pase) sin inventario.
 */
class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
            ['name' => 'Agua natural 600ml', 'price' => 15.00,  'is_service' => false, 'stock' => 50, 'min_stock' => 10],
            ['name' => 'Bebida isotónica',   'price' => 25.00,  'is_service' => false, 'stock' => 40, 'min_stock' => 10],
            ['name' => 'Barra de proteína',  'price' => 35.00,  'is_service' => false, 'stock' => 30, 'min_stock' => 8],
            ['name' => 'Proteína Whey 2lb',  'price' => 650.00, 'is_service' => false, 'stock' => 12, 'min_stock' => 5],
            // Stock bajo a propósito (1-5 piezas) para ver la alerta del dashboard
            ['name' => 'Creatina 300g',      'price' => 400.00, 'is_service' => false, 'stock' => 3,  'min_stock' => 5],
            // Servicio: pase de un día, sin inventario ni imagen
            ['name' => 'Pase diario',        'price' => 60.00,  'is_service' => true,  'stock' => 0,  'min_stock' => 0],
        ];

        foreach ($productos as $p) {
            Product::firstOrCreate(['name' => $p['name']], $p);
        }
    }
}

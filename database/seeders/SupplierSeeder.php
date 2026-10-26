<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $proveedores = [
            ['name' => 'Coca-Cola FEMSA',       'contact_name' => 'Jorge Ancira',  'phone' => '5544112233', 'email' => 'ventas@femsa.com'],
            ['name' => 'Optimum Nutrition',     'contact_name' => 'Ana Beltrán',   'phone' => '5566778899', 'email' => 'mx@optimum.com'],
            ['name' => 'Distribuidora El Músculo','contact_name' => 'Raúl Cano',   'phone' => '5500112233', 'email' => 'pedidos@elmusculo.mx'],
        ];

        foreach ($proveedores as $p) {
            Supplier::firstOrCreate(['name' => $p['name']], $p);
        }
    }
}

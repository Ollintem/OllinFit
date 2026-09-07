<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Plan Mensual (Borde Azul)
        Plan::create([
            'name' => 'Mensual',
            'price' => 500.00,
            'description' => 'Acceso completo a todas las áreas del gimnasio. Cobro recurrente o pago único sin plazos forzosos.',
            'duration_days' => 30,
            'highlight_text' => null, // No tiene texto destacado
            'color' => 'blue',
            'is_active' => true,
        ]);

        // 2. Plan Semestral (Borde Naranja)
        Plan::create([
            'name' => 'Semestral',
            'price' => 2500.00,
            'description' => 'Acceso ininterrumpido por 6 meses. Incluye asesoría nutricional inicial y un ahorro del 16% comparado con el plan mensual.',
            'duration_days' => 180,
            'highlight_text' => 'Ahorro del 16% vs mensual',
            'color' => 'orange',
            'is_active' => true,
        ]);
    }
}
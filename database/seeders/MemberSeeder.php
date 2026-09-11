<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\Plan;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Obtenemos los planes reales de tu BD (basados en tu PlanSeeder)
        $planMensual = Plan::where('name', 'Mensual')->first();
        $planSemestral = Plan::where('name', 'Semestral')->first();

        $hoy = Carbon::today();

        // 2. Socio 1: Estado ACTIVO (Vence en 20 días)
        Member::create([
            'folio' => 'MX-9021',
            'name' => 'Alejandro',
            'last_name' => 'Ruiz',
            'plan_id' => $planMensual->id, // Le asignamos el Mensual
            'expiration_date' => $hoy->copy()->addDays(20), 
            'is_active' => true,
        ]);

        // 3. Socio 2: Estado VENCIDO (Venció hace 10 días)
        Member::create([
            'folio' => 'MX-7411',
            'name' => 'Mateo',
            'last_name' => 'Gómez',
            'plan_id' => $planSemestral->id, // Le asignamos el Semestral
            'expiration_date' => $hoy->copy()->subDays(10), 
            'is_active' => true,
        ]);

        // 4. Socio 3: Estado POR VENCER (Vence en 3 días)
        Member::create([
            'folio' => 'MX-9022',
            'name' => 'Valeria',
            'last_name' => 'Flores',
            'plan_id' => $planMensual->id,
            'expiration_date' => $hoy->copy()->addDays(3), 
            'is_active' => true,
        ]);
    }
}
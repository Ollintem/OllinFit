<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Creamos al administrador principal
        User::create([
            'name' => 'Eduardo',
            'email' => 'admin@ollinfit.com',
            'password' => Hash::make('password123'), // Contraseña de prueba
        ]);

        // Mandamos llamar al seeder de planes que ya tenías hecho
        $this->call([
            PlanSeeder::class,
        ]);
    }
}
<?php

namespace Tests\Concerns;

use App\Models\CashRegister;
use App\Models\Plan;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Spatie\Permission\PermissionRegistrar;

trait PreparaDominio
{
    protected function sembrarRoles(): void
    {
        $this->seed(RolePermissionSeeder::class);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    protected function usuario(string $rol): User
    {
        $u = User::factory()->create(['is_active' => true]);
        $u->assignRole($rol);

        return $u;
    }

    protected function abrirCaja(User $u, float $fondo = 500): CashRegister
    {
        return CashRegister::create([
            'user_id' => $u->id,
            'opening_amount' => $fondo,
            'status' => 'abierta',
            'opened_at' => now(),
        ]);
    }

    /** Product::$fillable no incluye is_service/description, por eso forceCreate. */
    protected function producto(array $attrs = []): Product
    {
        return Product::forceCreate(array_merge([
            'name' => 'Producto ' . uniqid(),
            'price' => 25.50,
            'is_service' => false,
            'stock' => 10,
            'min_stock' => 2,
        ], $attrs));
    }

    protected function plan(array $attrs = []): Plan
    {
        return Plan::create(array_merge([
            'name' => 'Mensual',
            'price' => 500,
            'description' => 'Plan de prueba',
            'duration_value' => 1,
            'duration_unit' => 'months',
            'is_active' => true,
        ], $attrs));
    }
}

<?php

namespace Tests\Feature;

use App\Models\CashRegister;
use App\Models\Member;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class CajaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function usuario(array $permisos = [], bool $admin = false): User
    {
        $u = User::factory()->create(['is_active' => true]);
        if ($admin) {
            $u->assignRole('Administrador');
        } else {
            $u->givePermissionTo($permisos);
        }
        return $u;
    }

    private function abrirCaja(User $user, float $fondo = 500): CashRegister
    {
        return CashRegister::create([
            'user_id' => $user->id,
            'opening_amount' => $fondo,
            'status' => 'abierta',
            'opened_at' => now(),
        ]);
    }

    public function test_la_venta_del_pos_queda_ligada_a_la_caja_abierta(): void
    {
        $cajero = $this->usuario(['usar_pos']);
        $caja = $this->abrirCaja($cajero);
        $prod = Product::forceCreate(['name' => 'Agua', 'price' => 15, 'is_service' => false, 'stock' => 10, 'min_stock' => 2]);

        $this->actingAs($cajero)->post(route('sales.store'), [
            'cart_data' => json_encode([['id' => $prod->id, 'quantity' => 2, 'price' => 15]]),
            'payment_method' => 'efectivo',
        ])->assertOk();

        $this->assertSame($caja->id, Sale::first()->cash_register_id);
    }

    public function test_el_pago_de_alta_de_socio_queda_ligado_a_la_caja(): void
    {
        $cajero = $this->usuario(['crear_socios']);
        $caja = $this->abrirCaja($cajero);
        $plan = Plan::create(['name' => 'Mensual', 'price' => 500, 'duration_value' => 1, 'duration_unit' => 'months', 'is_active' => true]);

        $this->actingAs($cajero)->post(route('socios.store'), [
            'name' => 'Ana', 'last_name' => 'López', 'phone' => '4491234567',
            'plan_id' => $plan->id, 'payment_method' => 'efectivo', 'amount_received' => 500,
        ])->assertRedirect();

        $this->assertSame($caja->id, Payment::first()->cash_register_id);
    }

    public function test_el_esperado_se_calcula_por_caja_al_cerrar(): void
    {
        $cajero = $this->usuario(['operar_caja']);
        $caja = $this->abrirCaja($cajero, 500);
        // Ingreso en efectivo ligado a ESTA caja.
        Sale::create(['user_id' => $cajero->id, 'cash_register_id' => $caja->id, 'total' => 200, 'payment_method' => 'Efectivo']);
        // Ruido: una venta de OTRA caja NO debe contar en este corte.
        $otraCaja = CashRegister::create(['user_id' => $cajero->id, 'opening_amount' => 0, 'status' => 'cerrada', 'opened_at' => now()->subDay(), 'closed_at' => now()->subDay()]);
        Sale::create(['user_id' => $cajero->id, 'cash_register_id' => $otraCaja->id, 'total' => 999, 'payment_method' => 'Efectivo']);

        $this->actingAs($cajero)->post(route('caja.close'), ['closing_amount' => 700])->assertRedirect();

        $caja->refresh();
        $this->assertSame('cerrada', $caja->status);
        $this->assertEquals(700, $caja->expected_amount); // 500 fondo + 200 (solo de esta caja)
        $this->assertEquals(0, $caja->difference);
    }

    public function test_un_cajero_no_puede_cerrar_el_turno_de_otro(): void
    {
        $dueño = $this->usuario(['operar_caja']);
        $otro = $this->usuario(['operar_caja']);
        $caja = $this->abrirCaja($dueño);

        $this->actingAs($otro)->post(route('caja.close'), ['closing_amount' => 500])
            ->assertSessionHas('error');

        $this->assertSame('abierta', $caja->refresh()->status); // sigue abierta
    }

    public function test_el_administrador_si_puede_cerrar_un_turno_ajeno(): void
    {
        $cajero = $this->usuario(['operar_caja']);
        $admin = $this->usuario(admin: true);
        $caja = $this->abrirCaja($cajero);

        $this->actingAs($admin)->post(route('caja.close'), ['closing_amount' => 500])->assertRedirect();

        $this->assertSame('cerrada', $caja->refresh()->status);
    }
}

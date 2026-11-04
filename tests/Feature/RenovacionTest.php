<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\Payment;
use App\Models\Sale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\PreparaDominio;
use Tests\TestCase;

class RenovacionTest extends TestCase
{
    use RefreshDatabase, PreparaDominio;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sembrarRoles();
        $this->user = $this->usuario('Recepcionista');
        $this->abrirCaja($this->user);
    }

    private function socio($plan, $exp = null, string $folio = 'MX-9001'): Member
    {
        return Member::create([
            'folio' => $folio, 'name' => 'Ana', 'last_name' => 'Ruiz', 'phone' => '5550001111',
            'plan_id' => $plan->id, 'expiration_date' => $exp ?? now()->subDays(3)->toDateString(), 'is_active' => true,
        ]);
    }

    public function test_plan_inexistente_422_sin_pago(): void
    {
        $plan = $this->plan();
        $m = $this->socio($plan);
        $this->actingAs($this->user)->postJson(route('socios.renovar', $m->id), [
            'payment_method' => 'tarjeta', 'plan_id' => 9999,
        ])->assertStatus(422);
        $this->assertSame(0, Payment::count());
    }

    public function test_efectivo_insuficiente_rechaza(): void
    {
        $plan = $this->plan(['price' => 500]);
        $exp = now()->subDays(3)->toDateString();
        $m = $this->socio($plan, $exp);
        $this->actingAs($this->user)->from('/socios')->post(route('socios.renovar', $m->id), [
            'payment_method' => 'efectivo', 'amount_received' => 100, 'plan_id' => $plan->id,
        ])->assertRedirect('/socios')->assertSessionHas('error');
        $this->assertSame(0, Payment::count());
        $this->assertSame($exp, $m->fresh()->expiration_date->toDateString());
    }

    public function test_renovacion_exitosa_extiende_y_crea_un_solo_payment_sin_sale(): void
    {
        $plan = $this->plan(['price' => 500, 'duration_value' => 1, 'duration_unit' => 'months']);
        $m = $this->socio($plan);
        $this->actingAs($this->user)->post(route('socios.renovar', $m->id), [
            'payment_method' => 'efectivo', 'amount_received' => 600, 'plan_id' => $plan->id,
        ])->assertSessionHasNoErrors()->assertSessionHas('success');

        $esperada = $plan->extend(now())->toDateString(); // vencido: cuenta desde hoy
        $this->assertSame($esperada, $m->fresh()->expiration_date->toDateString());
        $this->assertSame(1, Payment::count());
        $p = Payment::first();
        $this->assertEquals(500, $p->amount);
        $this->assertSame('Efectivo', $p->payment_method);
        $this->assertSame(0, Sale::count(), 'Renovar no debe crear Sale (doble conteo)');
    }

    public function test_renovar_antes_de_vencer_acumula_desde_vencimiento_actual(): void
    {
        $plan = $this->plan(['price' => 300, 'duration_value' => 30, 'duration_unit' => 'days']);
        $exp = now()->addDays(10);
        $m = $this->socio($plan, $exp->toDateString());
        $this->actingAs($this->user)->post(route('socios.renovar', $m->id), [
            'payment_method' => 'tarjeta', 'plan_id' => $plan->id,
        ]);
        $this->assertSame($exp->copy()->startOfDay()->addDays(30)->toDateString(), $m->fresh()->expiration_date->toDateString());
    }

    public function test_ticket_del_propio_socio_200(): void
    {
        $plan = $this->plan();
        $m = $this->socio($plan);
        $pago = Payment::create(['member_id' => $m->id, 'plan_name' => 'Mensual', 'amount' => 500, 'payment_method' => 'Efectivo', 'folio_pago' => 'TXN-AAA']);
        $this->actingAs($this->user)->get(route('socios.ticket', [$m->id, $pago->id]))
            ->assertOk()->assertSee('TXN-AAA');
    }

    public function test_ticket_de_pago_de_otro_socio_404(): void
    {
        $plan = $this->plan();
        $a = $this->socio($plan, null, 'MX-9001');
        $b = $this->socio($plan, null, 'MX-9002');
        $pagoB = Payment::create(['member_id' => $b->id, 'plan_name' => 'Mensual', 'amount' => 500, 'payment_method' => 'Efectivo', 'folio_pago' => 'TXN-BBB']);
        $this->actingAs($this->user)->get(route('socios.ticket', [$a->id, $pagoB->id]))->assertNotFound();
    }
}

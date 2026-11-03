<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\Payment;
use App\Models\Sale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\PreparaDominio;
use Tests\TestCase;

class IngresosTest extends TestCase
{
    use RefreshDatabase, PreparaDominio;

    private function altaSocio()
    {
        $this->sembrarRoles();
        $u = $this->usuario('Recepcionista');
        $this->abrirCaja($u);
        $plan = $this->plan(['price' => 750]);

        $resp = $this->actingAs($u)->post(route('socios.store'), [
            'name' => 'Luis', 'last_name' => 'Perez', 'phone' => '5551234567',
            'plan_id' => $plan->id, 'payment_method' => 'efectivo', 'amount_received' => 1000,
        ]);

        return [$u, $plan, $resp];
    }

    public function test_alta_de_socio_crea_un_payment_y_ningun_sale(): void
    {
        [, $plan, $resp] = $this->altaSocio();
        $resp->assertSessionHasNoErrors()->assertRedirect(route('socios.index'));

        $this->assertSame(1, Member::count());
        $this->assertSame(1, Payment::count());
        $this->assertSame(0, Sale::count());
        $this->assertEquals($plan->price, Payment::first()->amount);
        $this->assertSame('MX-9001', Member::first()->folio);
    }

    public function test_alta_con_efectivo_insuficiente_no_crea_nada(): void
    {
        $this->sembrarRoles();
        $u = $this->usuario('Recepcionista');
        $this->abrirCaja($u);
        $plan = $this->plan(['price' => 750]);
        $this->actingAs($u)->post(route('socios.store'), [
            'name' => 'Luis', 'last_name' => 'Perez', 'phone' => '5551234567',
            'plan_id' => $plan->id, 'payment_method' => 'efectivo', 'amount_received' => 100,
        ])->assertSessionHas('error');
        $this->assertSame(0, Member::count());
        $this->assertSame(0, Payment::count());
    }

    public function test_total_de_ingresos_del_dia_no_cuenta_la_membresia_dos_veces(): void
    {
        [$u] = $this->altaSocio();
        $this->assertEquals(750, Payment::sum('amount') + Sale::sum('total'));

        // Recepcionista no tiene exportar_reportes
        $this->actingAs($u)->get(route('reports.export'))->assertForbidden();

        $admin = $this->usuario('Administrador');
        $r = $this->actingAs($admin)->get(route('reports.export'));
        $r->assertOk();
        $this->assertEquals(750, $r->viewData('totalIngresos'));
    }

    public function test_pos_y_membresia_suman_cada_uno_una_vez(): void
    {
        [$u] = $this->altaSocio();
        $p = $this->producto(['price' => 100, 'stock' => 5]);
        $this->actingAs($u)->postJson(route('sales.store'), ['cart_data' => [['id' => $p->id, 'quantity' => 2]], 'payment_method' => 'efectivo'])->assertOk();

        $admin = $this->usuario('Administrador');
        $r = $this->actingAs($admin)->get(route('reports.export'));
        $this->assertEquals(950, $r->viewData('totalIngresos'));
        $this->assertEquals(950, $r->viewData('efectivo'));
    }
}

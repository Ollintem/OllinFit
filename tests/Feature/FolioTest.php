<?php

namespace Tests\Feature;

use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\PreparaDominio;
use Tests\TestCase;

class FolioTest extends TestCase
{
    use RefreshDatabase, PreparaDominio;

    public function test_dos_altas_generan_folios_distintos_con_formato(): void
    {
        $this->sembrarRoles();
        $u = $this->usuario('Recepcionista');
        $this->abrirCaja($u);
        $plan = $this->plan(['price' => 500]);

        foreach (['Ana', 'Beto'] as $nombre) {
            $this->actingAs($u)->post(route('socios.store'), [
                'name' => $nombre, 'last_name' => 'Prueba', 'phone' => '5551234567',
                'plan_id' => $plan->id, 'payment_method' => 'efectivo', 'amount_received' => 500,
            ])->assertSessionHasNoErrors();
        }

        $folios = Member::orderBy('id')->pluck('folio')->all();

        $this->assertCount(2, $folios);
        $this->assertNotSame($folios[0], $folios[1]);          // nunca colisionan
        foreach ($folios as $folio) {
            $this->assertMatchesRegularExpression('/^MX-\d{4}$/', $folio); // mismo formato
        }
        // El folio se deriva del id real (id + 9000): no quedan folios temporales.
        $this->assertSame(['MX-9001', 'MX-9002'], $folios);
    }
}

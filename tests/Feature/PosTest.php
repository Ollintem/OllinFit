<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\PreparaDominio;
use Tests\TestCase;

class PosTest extends TestCase
{
    use RefreshDatabase, PreparaDominio;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sembrarRoles();
        $this->cajero = $this->usuario('Cajero');
        $this->abrirCaja($this->cajero);
    }

    private $cajero;

    private function vender(array $cart, array $extra = [])
    {
        return $this->actingAs($this->cajero)->postJson(route('sales.store'), array_merge(['cart_data' => $cart, 'payment_method' => 'efectivo'], $extra));
    }

    private function assertSinVenta(): void
    {
        $this->assertSame(0, Sale::count());
        $this->assertSame(0, SaleItem::count());
    }

    public function test_carrito_vacio_422(): void
    {
        $this->vender([])->assertStatus(422);
        $this->assertSinVenta();
    }

    public function test_carrito_ausente_422(): void
    {
        $this->actingAs($this->cajero)->postJson(route('sales.store'), ['payment_method' => 'efectivo'])->assertStatus(422);
        $this->assertSinVenta();
    }

    public function test_cantidad_cero_y_negativa_422(): void
    {
        $p = $this->producto(['stock' => 10]);
        $this->vender([['id' => $p->id, 'quantity' => 0]])->assertStatus(422);
        $this->vender([['id' => $p->id, 'quantity' => -3]])->assertStatus(422);
        $this->assertSinVenta();
        $this->assertSame(10, $p->fresh()->stock);
    }

    public function test_producto_inexistente_422(): void
    {
        $this->vender([['id' => 99999, 'quantity' => 1]])->assertStatus(422);
        $this->assertSinVenta();
    }

    public function test_precio_se_toma_de_la_bd_no_del_cliente(): void
    {
        $p = $this->producto(['price' => 100, 'stock' => 5]);
        $this->vender([['id' => $p->id, 'quantity' => 2, 'price' => 0.01]])->assertOk()->assertJson(['success' => true]);

        $sale = Sale::first();
        $this->assertEquals(200, $sale->total);
        $this->assertEquals(100, SaleItem::first()->price);
    }

    public function test_stock_insuficiente_422(): void
    {
        $p = $this->producto(['stock' => 3]);
        $this->vender([['id' => $p->id, 'quantity' => 4]])->assertStatus(422);
        $this->assertSinVenta();
        $this->assertSame(3, $p->fresh()->stock);
    }

    public function test_lineas_repetidas_que_exceden_stock_422(): void
    {
        $p = $this->producto(['stock' => 5]);
        $this->vender([['id' => $p->id, 'quantity' => 3], ['id' => $p->id, 'quantity' => 3]])->assertStatus(422);
        $this->assertSinVenta();
        $this->assertSame(5, $p->fresh()->stock);
    }

    public function test_venta_exitosa_descuenta_stock_y_registra_usuario(): void
    {
        $p = $this->producto(['price' => 30, 'stock' => 10]);
        $r = $this->vender([['id' => $p->id, 'quantity' => 4]], ['payment_method' => 'tarjeta']);
        $r->assertOk()->assertJson(['success' => true]);

        $sale = Sale::first();
        $this->assertSame($this->cajero->id, (int) $sale->user_id);
        $this->assertEquals(120, $sale->total);
        $this->assertSame('Tarjeta', $sale->payment_method);
        $this->assertSame(1, SaleItem::where('sale_id', $sale->id)->count());
        $this->assertSame(4, (int) SaleItem::first()->quantity);
        $this->assertSame(6, $p->fresh()->stock);
    }

    public function test_servicio_no_descuenta_stock(): void
    {
        $p = $this->producto(['price' => 50, 'is_service' => true, 'stock' => 0]);
        $this->vender([['id' => $p->id, 'quantity' => 2]])->assertOk();
        $this->assertSame(0, $p->fresh()->stock);
        $this->assertEquals(100, Sale::first()->total);
    }

    public function test_sin_permiso_usar_pos_403(): void
    {
        $entrenador = $this->usuario('Entrenador');
        $p = $this->producto();
        $this->actingAs($entrenador)->postJson(route('sales.store'), ['cart_data' => [['id' => $p->id, 'quantity' => 1]]])->assertForbidden();
        $this->assertSinVenta();
    }

    public function test_sin_caja_abierta_redirige_a_caja(): void
    {
        \App\Models\CashRegister::query()->update(['status' => 'cerrada']);
        $p = $this->producto();
        $this->actingAs($this->cajero)
            ->post(route('sales.store'), ['cart_data' => [['id' => $p->id, 'quantity' => 1]]])
            ->assertRedirect(route('caja.index'));
        $this->assertSinVenta();
        $this->assertSame(10, $p->fresh()->stock);
    }
}

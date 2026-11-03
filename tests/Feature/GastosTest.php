<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\ExpenseItem;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\PreparaDominio;
use Tests\TestCase;

class GastosTest extends TestCase
{
    use RefreshDatabase, PreparaDominio;

    private $gerente;
    private $caja;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sembrarRoles();
        $this->gerente = $this->usuario('Gerente');
        $this->caja = $this->abrirCaja($this->gerente);
    }

    private function comprar($proveedor, array $items, float $monto, string $metodo = 'Efectivo')
    {
        return $this->actingAs($this->gerente)->post(route('gastos.store'), [
            'type' => 'compra_proveedor', 'amount' => $monto, 'payment_method' => $metodo,
            'description' => 'Reabasto', 'supplier_id' => $proveedor->id, 'items_json' => json_encode($items),
        ]);
    }

    public function test_compra_proveedor_incrementa_stock_y_liga_a_caja(): void
    {
        $s = Supplier::create(['name' => 'Prov']);
        $a = $this->producto(['stock' => 2]);
        $b = $this->producto(['stock' => 0]);

        $this->comprar($s, [['id' => $a->id, 'qty' => 5, 'cost' => 10], ['id' => $b->id, 'qty' => 3, 'cost' => 20]], 110)
            ->assertRedirect(route('gastos.index'));

        $this->assertSame(7, $a->fresh()->stock);
        $this->assertSame(3, $b->fresh()->stock);
        $e = Expense::first();
        $this->assertSame($this->caja->id, (int) $e->cash_register_id);
        $this->assertSame(2, ExpenseItem::where('expense_id', $e->id)->count());
    }

    public function test_eliminar_gasto_revierte_stock_y_borra_detalle(): void
    {
        $s = Supplier::create(['name' => 'Prov']);
        $a = $this->producto(['stock' => 2]);
        $this->comprar($s, [['id' => $a->id, 'qty' => 5, 'cost' => 10]], 50);
        $this->assertSame(7, $a->fresh()->stock);

        $e = Expense::first();
        $this->actingAs($this->gerente)->delete(route('gastos.destroy', $e->id))->assertRedirect(route('gastos.index'));

        $this->assertSame(2, $a->fresh()->stock);
        $this->assertSame(0, ExpenseItem::count());
        $this->assertSame(0, Expense::count());
    }

    public function test_proveedor_con_compras_no_se_puede_eliminar(): void
    {
        $s = Supplier::create(['name' => 'Prov']);
        $a = $this->producto();
        $this->comprar($s, [['id' => $a->id, 'qty' => 1, 'cost' => 10]], 10);

        $this->actingAs($this->gerente)->delete(route('proveedores.destroy', $s->id))
            ->assertRedirect(route('proveedores.index'))->assertSessionHas('error');
        $this->assertNotNull(Supplier::find($s->id));
    }

    public function test_proveedor_sin_compras_si_se_elimina(): void
    {
        $s = Supplier::create(['name' => 'Libre']);
        $this->actingAs($this->gerente)->delete(route('proveedores.destroy', $s->id))->assertSessionHas('success');
        $this->assertNull(Supplier::find($s->id));
    }

    public function test_gasto_en_efectivo_sin_caja_abierta_no_se_registra(): void
    {
        $this->caja->update(['status' => 'cerrada']);
        $this->actingAs($this->gerente)->post(route('gastos.store'), [
            'type' => 'gasto_general', 'concept' => 'Luz', 'amount' => 100, 'payment_method' => 'Efectivo', 'description' => 'x',
        ])->assertRedirect(route('caja.index'));
        $this->assertSame(0, Expense::count());
    }

    public function test_gasto_general_se_liga_a_la_caja_abierta(): void
    {
        $this->actingAs($this->gerente)->post(route('gastos.store'), [
            'type' => 'gasto_general', 'concept' => 'Luz', 'amount' => 100, 'payment_method' => 'Efectivo', 'description' => 'x',
        ])->assertRedirect(route('gastos.index'));
        $this->assertSame($this->caja->id, (int) Expense::first()->cash_register_id);
    }
}

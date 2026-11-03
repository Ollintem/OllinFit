<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\Concerns\PreparaDominio;
use Tests\TestCase;

class RbacTest extends TestCase
{
    use RefreshDatabase, PreparaDominio;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sembrarRoles();
    }

    public function test_cajero_403_en_empleados_y_ok_en_pos(): void
    {
        $c = $this->usuario('Cajero');
        $this->abrirCaja($c);
        $this->actingAs($c)->get(route('empleados.index'))->assertForbidden();
        $this->actingAs($c)->get(route('sales.index'))->assertOk();
    }

    public function test_administrador_pasa_todo(): void
    {
        $a = $this->usuario('Administrador');
        $this->abrirCaja($a);
        $this->actingAs($a)->get(route('empleados.index'))->assertOk();
        $this->actingAs($a)->get(route('roles.create'))->assertOk();
        $this->actingAs($a)->get(route('reports.index'))->assertOk();
        $this->actingAs($a)->get(route('sales.index'))->assertOk();
    }

    public function test_gerente_no_accede_a_gestion_de_roles(): void
    {
        $g = $this->usuario('Gerente');
        $this->actingAs($g)->get(route('roles.create'))->assertForbidden();
    }

    public function test_permiso_denegado_por_empleado_bloquea_aunque_el_rol_lo_otorgue(): void
    {
        $c1 = $this->usuario('Cajero');
        $c2 = $this->usuario('Cajero');
        $this->abrirCaja($c1);

        $c1->deniedPermissions()->sync([Permission::where('name', 'usar_pos')->first()->id]);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $p = $this->producto();
        $this->actingAs($c1->fresh())->get(route('sales.index'))->assertForbidden();
        $this->actingAs($c1->fresh())->postJson(route('sales.store'), ['cart_data' => [['id' => $p->id, 'quantity' => 1]]])->assertForbidden();

        $this->actingAs($c2->fresh())->get(route('sales.index'))->assertOk();
    }

    public function test_permiso_directo_da_acceso(): void
    {
        $c = $this->usuario('Cajero');
        $this->actingAs($c)->get(route('reports.index'))->assertForbidden();

        $c->givePermissionTo('ver_reportes');
        $this->actingAs($c->fresh())->get(route('reports.index'))->assertOk();
    }

    public function test_gerente_no_puede_crear_administrador(): void
    {
        $g = $this->usuario('Gerente');
        $this->actingAs($g)->post(route('empleados.store'), [
            'name' => 'X', 'last_name' => 'Y', 'email' => 'x@y.com', 'password' => 'password123', 'role' => 'Administrador',
        ])->assertForbidden();
        $this->assertNull(User::where('email', 'x@y.com')->first());
    }

    public function test_gerente_si_puede_crear_empleado_normal(): void
    {
        $g = $this->usuario('Gerente');
        $this->actingAs($g)->post(route('empleados.store'), [
            'name' => 'X', 'last_name' => 'Y', 'email' => 'x@y.com', 'password' => 'password123', 'role' => 'Cajero', 'is_active' => 1,
        ])->assertRedirect(route('empleados.index'));
        $this->assertTrue(User::where('email', 'x@y.com')->first()->hasRole('Cajero'));
    }

    public function test_gerente_no_puede_editar_ni_cambiar_password_de_administrador(): void
    {
        $g = $this->usuario('Gerente');
        $a = $this->usuario('Administrador');
        $hash = $a->password;
        $this->actingAs($g)->put(route('empleados.update', $a->id), [
            'name' => 'Hack', 'last_name' => 'Hack', 'email' => $a->email, 'password' => 'nuevaclave123', 'role' => 'Administrador',
        ])->assertForbidden();
        $this->assertSame($hash, $a->fresh()->password);
        $this->assertNotSame('Hack', $a->fresh()->name);
    }

    public function test_gerente_no_puede_eliminar_administrador(): void
    {
        $g = $this->usuario('Gerente');
        $a = $this->usuario('Administrador');
        $this->actingAs($g)->delete(route('empleados.destroy', $a->id))->assertForbidden();
        $this->assertNotNull(User::find($a->id));
    }

    public function test_no_se_puede_auto_eliminar(): void
    {
        $g = $this->usuario('Gerente');
        $this->actingAs($g)->delete(route('empleados.destroy', $g->id))
            ->assertRedirect(route('empleados.index'))->assertSessionHas('error');
        $this->assertNotNull(User::find($g->id));
    }

    public function test_no_se_puede_eliminar_al_ultimo_administrador(): void
    {
        // El seeder crea un unico Administrador (admin@ollinfit.com). Un segundo admin lo elimina
        // (permitido, quedan 1), y despues ese ultimo ya no puede eliminarse.
        $admin = User::where('email', 'admin@ollinfit.com')->first();
        $b = $this->usuario('Administrador');
        $this->actingAs($admin)->delete(route('empleados.destroy', $b->id))
            ->assertRedirect(route('empleados.index'))->assertSessionHas('success');
        $this->assertSame(1, User::role('Administrador')->count());

        // Con 1 solo admin, ni otro admin ficticio ni el propio pueden borrarlo.
        $this->actingAs($admin)->delete(route('empleados.destroy', $admin->id))->assertSessionHas('error');
        $this->assertNotNull(User::find($admin->id));
    }
}

<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class QrTokenTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function admin(): User
    {
        $u = User::factory()->create(['is_active' => true]);
        $u->assignRole('Administrador'); // pasa todos los permisos (Gate::before)
        return $u;
    }

    private function socioVigente(array $attrs = []): Member
    {
        $plan = Plan::create([
            'name' => 'Mensual', 'price' => 500,
            'duration_value' => 1, 'duration_unit' => 'months', 'is_active' => true,
        ]);

        return Member::create(array_merge([
            'folio' => 'MX-9001',
            'name' => 'Ana', 'last_name' => 'López',
            'phone' => '4491234567',
            'plan_id' => $plan->id,
            'expiration_date' => now()->addMonth(),
            'is_active' => true,
        ], $attrs));
    }

    public function test_al_crear_socio_se_genera_un_qr_token(): void
    {
        $socio = $this->socioVigente();

        $this->assertNotNull($socio->qr_token);
        $this->assertSame(40, strlen($socio->qr_token));
    }

    public function test_escanear_con_el_token_correcto_da_acceso(): void
    {
        $socio = $this->socioVigente();

        $this->actingAs($this->admin())
            ->postJson(route('acceso.scan'), ['codigo' => $socio->qr_token])
            ->assertOk()
            ->assertJson(['success' => true, 'is_inside' => true]);

        $this->assertTrue((bool) $socio->fresh()->is_inside);
    }

    public function test_escanear_con_el_folio_ya_no_da_acceso(): void
    {
        $socio = $this->socioVigente();

        $this->actingAs($this->admin())
            ->postJson(route('acceso.scan'), ['codigo' => $socio->folio])
            ->assertStatus(404)
            ->assertJson(['success' => false]);
    }

    public function test_regenerar_el_token_invalida_el_anterior(): void
    {
        $socio = $this->socioVigente();
        $tokenViejo = $socio->qr_token;

        $socio->regenerarQrToken();

        $this->assertNotSame($tokenViejo, $socio->fresh()->qr_token);

        // El QR anterior ya no abre el acceso.
        $this->actingAs($this->admin())
            ->postJson(route('acceso.scan'), ['codigo' => $tokenViejo])
            ->assertStatus(404);
    }

    public function test_el_scan_no_expone_el_qr_token(): void
    {
        $socio = $this->socioVigente();

        $resp = $this->actingAs($this->admin())
            ->postJson(route('acceso.scan'), ['codigo' => $socio->qr_token])
            ->assertOk();

        // La respuesta nunca debe contener el token secreto.
        $this->assertStringNotContainsString($socio->qr_token, $resp->getContent());
    }
}

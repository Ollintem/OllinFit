<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class IntegracionesTest extends TestCase
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
        $u->assignRole('Administrador');
        return $u;
    }

    public function test_setting_guarda_y_lee_y_cifra_los_sensibles(): void
    {
        Setting::put('mail_host', 'smtp.gmail.com');
        Setting::put('mail_password', 'secreto123', true);

        $this->assertSame('smtp.gmail.com', Setting::get('mail_host'));
        $this->assertSame('secreto123', Setting::get('mail_password'));

        // En la BD, el valor sensible NO está en texto plano.
        $this->assertNotSame('secreto123', Setting::where('key', 'mail_password')->first()->value);
    }

    public function test_el_panel_es_solo_para_administrador(): void
    {
        $cajero = User::factory()->create(['is_active' => true]);
        $cajero->givePermissionTo('usar_pos');

        $this->actingAs($cajero)->get(route('integraciones.index'))->assertForbidden();
        $this->actingAs($this->admin())->get(route('integraciones.index'))->assertOk();
    }

    public function test_guardar_correo_persiste_los_ajustes(): void
    {
        // El usuario solo escribe su correo y la App Password; lo técnico lo fija el sistema.
        $this->actingAs($this->admin())->put(route('integraciones.correo'), [
            'mail_from_address' => 'gym@gmail.com',
            'mail_password' => 'abcd efgh ijkl mnop', // con espacios a propósito
        ])->assertRedirect(route('integraciones.index'))->assertSessionHas('success');

        // La config de Gmail se fija automáticamente.
        $this->assertSame('smtp.gmail.com', Setting::get('mail_host'));
        $this->assertSame('465', Setting::get('mail_port'));
        $this->assertSame('smtps', Setting::get('mail_scheme'));
        // El usuario y el "de" quedan igual que el correo capturado.
        $this->assertSame('gym@gmail.com', Setting::get('mail_username'));
        $this->assertSame('gym@gmail.com', Setting::get('mail_from_address'));
        // La App Password se guarda SIN espacios.
        $this->assertSame('abcdefghijklmnop', Setting::get('mail_password'));
    }

    public function test_enviar_correo_de_prueba(): void
    {
        Mail::fake();

        $this->actingAs($this->admin())->post(route('integraciones.correo.prueba'), [
            'correo_prueba' => 'destino@example.com',
        ])->assertRedirect(route('integraciones.index'))->assertSessionHas('success');
    }

    public function test_activar_whatsapp_habilita_y_pone_defaults(): void
    {
        // "Conectar WhatsApp" habilita el envío y deja la config técnica por defecto.
        $this->actingAs($this->admin())->put(route('integraciones.whatsapp'))
            ->assertRedirect(route('integraciones.index'))->assertSessionHas('success');

        $this->assertSame('1', Setting::get('whatsapp_enabled'));
        $this->assertNotNull(Setting::get('whatsapp_url'));
        $this->assertNotNull(Setting::get('whatsapp_country_code'));
    }

    public function test_probar_whatsapp_requiere_estar_activo(): void
    {
        config(['services.whatsapp.enabled' => false]);

        $this->actingAs($this->admin())->post(route('integraciones.whatsapp.prueba'), [
            'telefono_prueba' => '4491234567',
        ])->assertRedirect(route('integraciones.index'))->assertSessionHas('error');
    }

    public function test_probar_whatsapp_envia_cuando_esta_activo(): void
    {
        config([
            'services.whatsapp.enabled' => true,
            'services.whatsapp.url' => 'http://ws.test',
            'services.whatsapp.token' => 'secreto',
            'services.whatsapp.country_code' => '52',
        ]);
        Http::fake(['ws.test/send' => Http::response(['ok' => true], 200)]);

        $this->actingAs($this->admin())->post(route('integraciones.whatsapp.prueba'), [
            'telefono_prueba' => '4491234567',
        ])->assertRedirect(route('integraciones.index'))->assertSessionHas('success');
    }

    public function test_estado_whatsapp_proxea_el_microservicio(): void
    {
        config(['services.whatsapp.url' => 'http://ws.test', 'services.whatsapp.token' => 'secreto']);
        Http::fake(['ws.test/qr' => Http::response(['connected' => false, 'qr' => 'data:image/png;base64,AAA'], 200)]);

        $this->actingAs($this->admin())
            ->getJson(route('integraciones.whatsapp.estado'))
            ->assertOk()
            ->assertJson(['reachable' => true, 'connected' => false, 'qr' => 'data:image/png;base64,AAA']);
    }

    public function test_estado_whatsapp_reporta_servicio_caido(): void
    {
        config(['services.whatsapp.url' => 'http://ws.test', 'services.whatsapp.token' => 'secreto']);
        Http::fake(fn () => throw new \Illuminate\Http\Client\ConnectionException('down'));

        $this->actingAs($this->admin())
            ->getJson(route('integraciones.whatsapp.estado'))
            ->assertOk()
            ->assertJson(['reachable' => false, 'connected' => false]);
    }
}

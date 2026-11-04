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
        $this->actingAs($this->admin())->put(route('integraciones.correo'), [
            'mail_host' => 'smtp.gmail.com',
            'mail_port' => 465,
            'mail_scheme' => 'smtps',
            'mail_username' => 'gym@gmail.com',
            'mail_password' => 'abcd efgh ijkl mnop', // con espacios a propósito
            'mail_from_address' => 'gym@gmail.com',
            'mail_from_name' => 'OllinFit',
        ])->assertRedirect(route('integraciones.index'))->assertSessionHas('success');

        $this->assertSame('smtp.gmail.com', Setting::get('mail_host'));
        $this->assertSame('465', Setting::get('mail_port'));
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

    public function test_guardar_whatsapp_persiste_los_ajustes(): void
    {
        $this->actingAs($this->admin())->put(route('integraciones.whatsapp'), [
            'whatsapp_enabled' => '1',
            'whatsapp_url' => 'http://127.0.0.1:3000',
            'whatsapp_token' => 'mi-token',
            'whatsapp_country_code' => '52',
        ])->assertRedirect(route('integraciones.index'))->assertSessionHas('success');

        $this->assertSame('1', Setting::get('whatsapp_enabled'));
        $this->assertSame('http://127.0.0.1:3000', Setting::get('whatsapp_url'));
        $this->assertSame('mi-token', Setting::get('whatsapp_token'));
        // El token se guarda cifrado.
        $this->assertNotSame('mi-token', Setting::where('key', 'whatsapp_token')->first()->value);
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

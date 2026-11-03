<?php

namespace Tests\Feature;

use App\Jobs\EnviarBienvenidaSocio;
use App\Models\Member;
use App\Models\Plan;
use App\Services\WhatsappSender;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WhatsappBienvenidaTest extends TestCase
{
    use RefreshDatabase;

    private function configurarWhatsapp(bool $enabled): void
    {
        config([
            'services.whatsapp.enabled' => $enabled,
            'services.whatsapp.url' => 'http://ws.test',
            'services.whatsapp.token' => 'secreto',
            'services.whatsapp.country_code' => '52',
        ]);
    }

    private function socio(array $attrs = []): Member
    {
        $plan = Plan::create([
            'name' => 'Mensual', 'price' => 500,
            'duration_value' => 1, 'duration_unit' => 'months', 'is_active' => true,
        ]);

        return Member::create(array_merge([
            'folio' => 'MX-9012',
            'name' => 'Ana', 'last_name' => 'López',
            'phone' => '4491234567',
            'plan_id' => $plan->id,
            'expiration_date' => now()->addMonth(),
            'is_active' => true,
        ], $attrs));
    }

    public function test_normaliza_el_telefono_a_formato_internacional(): void
    {
        $sender = new WhatsappSender();
        $this->assertSame('524491234567', $sender->normalizarTelefono('449 123 4567')); // 10 dígitos → +52
        $this->assertSame('524491234567', $sender->normalizarTelefono('+52 449 123 4567')); // ya con lada
        $this->assertNull($sender->normalizarTelefono('12345')); // insuficiente
    }

    public function test_envia_el_payload_correcto_cuando_esta_habilitado(): void
    {
        $this->configurarWhatsapp(true);
        Http::fake(['ws.test/*' => Http::response(['ok' => true], 200)]);

        $socio = $this->socio();
        dispatch_sync(new EnviarBienvenidaSocio($socio->id));

        Http::assertSent(function ($request) use ($socio) {
            return str_ends_with($request->url(), '/send')
                && $request['to'] === '524491234567'
                && $request['qr'] === $socio->folio
                && str_contains($request['message'], $socio->folio)
                && $request->hasHeader('Authorization', 'Bearer secreto');
        });
    }

    public function test_no_envia_nada_si_esta_deshabilitado(): void
    {
        $this->configurarWhatsapp(false);
        Http::fake();

        $socio = $this->socio();
        dispatch_sync(new EnviarBienvenidaSocio($socio->id));

        Http::assertNothingSent();
    }

    public function test_no_envia_si_el_socio_no_tiene_telefono(): void
    {
        $this->configurarWhatsapp(true);
        Http::fake();

        $socio = $this->socio(['phone' => null]);
        dispatch_sync(new EnviarBienvenidaSocio($socio->id));

        Http::assertNothingSent();
    }
}

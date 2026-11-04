<?php

namespace Tests\Feature;

use App\Jobs\EnviarBienvenidaSocio;
use App\Models\Member;
use App\Models\Plan;
use App\Notifications\BienvenidaSocio;
use App\Notifications\MembresiaPorVencer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AvisosMultiCanalTest extends TestCase
{
    use RefreshDatabase;

    private function whatsapp(bool $enabled): void
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
        $plan = Plan::create(['name' => 'Mensual', 'price' => 500, 'duration_value' => 1, 'duration_unit' => 'months', 'is_active' => true]);

        return Member::create(array_merge([
            'folio' => 'MX-' . fake()->unique()->numberBetween(9001, 9999),
            'name' => 'Ana', 'last_name' => 'López',
            'email' => 'ana@example.com', 'phone' => '4491234567',
            'plan_id' => $plan->id, 'expiration_date' => now()->addDays(3), 'is_active' => true,
        ], $attrs));
    }

    public function test_bienvenida_envia_correo(): void
    {
        Notification::fake();
        $this->whatsapp(false); // aislar el correo
        $socio = $this->socio();

        dispatch_sync(new EnviarBienvenidaSocio($socio->id));

        Notification::assertSentTo($socio, BienvenidaSocio::class);
    }

    public function test_bienvenida_no_envia_correo_si_el_socio_no_tiene_email(): void
    {
        Notification::fake();
        $this->whatsapp(false);
        $socio = $this->socio(['email' => null]);

        dispatch_sync(new EnviarBienvenidaSocio($socio->id));

        Notification::assertNothingSentTo($socio);
    }

    public function test_recordatorio_envia_whatsapp_de_texto(): void
    {
        $this->whatsapp(true);
        Http::fake(['ws.test/*' => Http::response(['ok' => true], 200)]);
        $socio = $this->socio(['email' => null]); // aislar el canal WhatsApp

        $this->artisan('socios:recordar-vencimientos')->assertSuccessful();

        Http::assertSent(function ($request) {
            return str_ends_with($request->url(), '/send')
                && $request['to'] === '524491234567'
                && ! isset($request['qr'])            // el recordatorio NO lleva QR
                && str_contains($request['message'], 'vence');
        });
    }

    public function test_recordatorio_envia_correo(): void
    {
        Notification::fake();
        $this->whatsapp(false);
        $socio = $this->socio(['phone' => null]); // aislar el canal correo

        $this->artisan('socios:recordar-vencimientos')->assertSuccessful();

        Notification::assertSentTo($socio, MembresiaPorVencer::class);
    }
}

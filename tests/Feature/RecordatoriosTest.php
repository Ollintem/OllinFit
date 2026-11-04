<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\Plan;
use App\Notifications\MembresiaPorVencer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RecordatoriosTest extends TestCase
{
    use RefreshDatabase;

    private function plan(): Plan
    {
        return Plan::create([
            'name' => 'Mensual',
            'price' => 500,
            'duration_value' => 1,
            'duration_unit' => 'months',
            'is_active' => true,
        ]);
    }

    private function socio(array $attrs): Member
    {
        return Member::create(array_merge([
            'folio' => 'MX-' . fake()->unique()->numberBetween(9001, 9999),
            'name' => 'Ana',
            'last_name' => 'López',
            'email' => 'ana@example.com',
            'plan_id' => $this->plan()->id,
            'is_active' => true,
        ], $attrs));
    }

    public function test_avisa_a_socio_por_vencer_dentro_de_la_ventana(): void
    {
        Notification::fake();
        $socio = $this->socio(['expiration_date' => now()->addDays(3)]);

        $this->artisan('socios:recordar-vencimientos')->assertSuccessful();

        Notification::assertSentTo($socio, MembresiaPorVencer::class);
        $this->assertSame(
            $socio->expiration_date->toDateString(),
            $socio->fresh()->reminder_sent_for?->toDateString()
        );
    }

    public function test_no_avisa_si_vence_fuera_de_la_ventana(): void
    {
        Notification::fake();
        $socio = $this->socio(['expiration_date' => now()->addDays(30)]);

        $this->artisan('socios:recordar-vencimientos')->assertSuccessful();

        Notification::assertNothingSentTo($socio);
    }

    public function test_no_reenvia_el_mismo_aviso_en_una_segunda_corrida(): void
    {
        Notification::fake();
        $socio = $this->socio(['expiration_date' => now()->addDays(2)]);

        $this->artisan('socios:recordar-vencimientos');
        $this->artisan('socios:recordar-vencimientos'); // segunda corrida el mismo ciclo

        Notification::assertSentToTimes($socio, MembresiaPorVencer::class, 1);
    }

    public function test_ignora_inactivos_y_sin_correo(): void
    {
        Notification::fake();
        $inactivo = $this->socio(['expiration_date' => now()->addDays(2), 'is_active' => false]);
        $sinCorreo = $this->socio(['expiration_date' => now()->addDays(2), 'email' => null]);

        $this->artisan('socios:recordar-vencimientos')->assertSuccessful();

        Notification::assertNothingSentTo($inactivo);
        Notification::assertNothingSentTo($sinCorreo);
    }

    public function test_al_renovar_se_vuelve_a_habilitar_el_aviso(): void
    {
        Notification::fake();
        $socio = $this->socio(['expiration_date' => now()->addDays(2)]);

        $this->artisan('socios:recordar-vencimientos'); // 1er aviso

        // Simula renovación: nueva fecha de vencimiento dentro de una nueva ventana
        $socio->update(['expiration_date' => now()->addDays(3)]);

        $this->artisan('socios:recordar-vencimientos'); // debe avisar de nuevo

        Notification::assertSentToTimes($socio, MembresiaPorVencer::class, 2);
    }
}

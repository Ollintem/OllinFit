<?php

namespace App\Console\Commands;

use App\Models\Member;
use App\Notifications\MembresiaPorVencer;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RecordarVencimientos extends Command
{
    protected $signature = 'socios:recordar-vencimientos {--dias=5 : Días de anticipación del aviso}';

    protected $description = 'Envía por email un recordatorio a los socios cuya membresía está por vencer.';

    public function handle(): int
    {
        $dias = max(0, (int) $this->option('dias'));
        $hoy = Carbon::today();
        $limite = $hoy->copy()->addDays($dias);

        // Socios activos, con correo, que vencen dentro de la ventana [hoy, hoy+dias].
        $socios = Member::query()
            ->where('is_active', true)
            ->whereNotNull('email')
            ->whereBetween('expiration_date', [$hoy->toDateString(), $limite->toDateString()])
            ->with('plan')
            ->get();

        $enviados = 0;

        foreach ($socios as $socio) {
            // Evita reenviar el mismo aviso: solo si aún no se notificó para ESTA fecha de vencimiento.
            // Al renovar, expiration_date cambia y el recordatorio se vuelve a habilitar solo.
            $yaAvisado = $socio->reminder_sent_for
                && $socio->reminder_sent_for->toDateString() === $socio->expiration_date->toDateString();

            if ($yaAvisado) {
                continue;
            }

            $diasRestantes = (int) $hoy->diffInDays($socio->expiration_date, false);

            $socio->notify(new MembresiaPorVencer($diasRestantes));

            $socio->forceFill(['reminder_sent_for' => $socio->expiration_date])->save();
            $enviados++;
        }

        $this->info("Recordatorios enviados: {$enviados} (ventana: {$dias} días, socios en ventana: {$socios->count()}).");

        return self::SUCCESS;
    }
}

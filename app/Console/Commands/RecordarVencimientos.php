<?php

namespace App\Console\Commands;

use App\Models\Member;
use App\Notifications\MembresiaPorVencer;
use App\Services\WhatsappSender;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RecordarVencimientos extends Command
{
    protected $signature = 'socios:recordar-vencimientos {--dias=5 : Días de anticipación del aviso}';

    protected $description = 'Avisa por email y WhatsApp a los socios cuya membresía está por vencer.';

    public function handle(WhatsappSender $whatsapp): int
    {
        $dias = max(0, (int) $this->option('dias'));
        $hoy = Carbon::today();
        $limite = $hoy->copy()->addDays($dias);

        // Socios activos con correo o teléfono que vencen dentro de la ventana [hoy, hoy+dias].
        $socios = Member::query()
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNotNull('email')->orWhereNotNull('phone'))
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

            // Canal 1: correo.
            if ($socio->email) {
                $socio->notify(new MembresiaPorVencer($diasRestantes));
            }

            // Canal 2: WhatsApp (solo texto; si está habilitado y hay teléfono).
            if ($whatsapp->habilitado() && $socio->phone) {
                $plan = $socio->plan->name ?? 'tu membresía';
                $fecha = $socio->expiration_date?->format('d/m/Y');
                $cuando = $diasRestantes <= 0
                    ? 'vence hoy'
                    : ($diasRestantes === 1 ? 'vence mañana' : "vence en {$diasRestantes} días");

                $mensaje = "Hola {$socio->name}, tu membresía *{$plan}* {$cuando} (el {$fecha}). "
                    . 'Renueva a tiempo para no perder tu acceso al gimnasio. 💪 — OllinFit';

                $whatsapp->enviarTexto($socio->phone, $mensaje);
            }

            $socio->forceFill(['reminder_sent_for' => $socio->expiration_date])->save();
            $enviados++;
        }

        $this->info("Recordatorios enviados: {$enviados} (ventana: {$dias} días, socios en ventana: {$socios->count()}).");

        return self::SUCCESS;
    }
}

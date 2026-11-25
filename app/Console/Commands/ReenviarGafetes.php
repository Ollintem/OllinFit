<?php

namespace App\Console\Commands;

use App\Models\Member;
use App\Services\WhatsappSender;
use Illuminate\Console\Command;

/**
 * Reenvía el gafete (QR de acceso) por WhatsApp a los socios activos con teléfono.
 * Útil tras cambiar el QR a token: los gafetes impresos con el folio dejan de servir.
 */
class ReenviarGafetes extends Command
{
    protected $signature = 'socios:reenviar-gafetes {--dry-run : Solo muestra a quién se enviaría, sin enviar}';

    protected $description = 'Reenvía el gafete (QR) por WhatsApp a los socios activos con teléfono';

    public function handle(WhatsappSender $whatsapp): int
    {
        $dryRun = (bool) $this->option('dry-run');

        if (! $dryRun && ! $whatsapp->habilitado()) {
            $this->error('WhatsApp no está habilitado. Actívalo en Integraciones antes de reenviar.');
            return self::FAILURE;
        }

        $socios = Member::with('plan')
            ->where('is_active', true)
            ->whereNotNull('phone')
            ->get();

        $this->info("Socios objetivo (activos con teléfono): {$socios->count()}");

        $enviados = 0;
        foreach ($socios as $socio) {
            if ($dryRun) {
                $this->line("• {$socio->name} {$socio->last_name} — {$socio->phone}");
                continue;
            }

            $plan = $socio->plan->name ?? 'tu membresía';
            $vigencia = $socio->expiration_date?->format('d/m/Y');

            $mensaje = "¡Hola {$socio->name}! 👋 Te reenviamos tu *gafete digital* de *OllinFit*.\n\n"
                . "Membresía: *{$plan}*" . ($vigencia ? " (vigente hasta el {$vigencia})" : '') . ".\n"
                . "Folio: *{$socio->folio}*.\n\n"
                . "Muestra este código QR en recepción para registrar tu entrada. 💪";

            if ($whatsapp->enviarConQr($socio->phone, $mensaje, $socio->qr_token)) {
                $enviados++;
                $this->line("✓ {$socio->name} {$socio->last_name}");
            } else {
                $this->warn("✗ No se pudo enviar a {$socio->name} ({$socio->phone})");
            }
        }

        if ($dryRun) {
            $this->info('Modo dry-run: no se envió ningún mensaje.');
        } else {
            $this->info("Gafetes enviados: {$enviados}/{$socios->count()}");
        }

        return self::SUCCESS;
    }
}

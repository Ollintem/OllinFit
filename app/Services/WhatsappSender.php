<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Cliente del microservicio no oficial de WhatsApp (carpeta whatsapp-service/).
 * Laravel solo envía datos; el QR lo dibuja el servicio Node a partir del folio.
 */
class WhatsappSender
{
    public function habilitado(): bool
    {
        return (bool) config('services.whatsapp.enabled');
    }

    /**
     * Normaliza un teléfono a dígitos con lada de país (ej. 5214491234567).
     * Devuelve null si no hay suficientes dígitos.
     */
    public function normalizarTelefono(?string $telefono): ?string
    {
        $digitos = preg_replace('/\D+/', '', (string) $telefono);

        if (strlen($digitos) < 10) {
            return null;
        }

        $lada = preg_replace('/\D+/', '', (string) config('services.whatsapp.country_code', '52'));

        // Si viene de 10 dígitos (nacional), le anteponemos la lada del país.
        if (strlen($digitos) === 10 && $lada !== '') {
            $digitos = $lada . $digitos;
        }

        return $digitos;
    }

    /**
     * Envía un mensaje con un QR (generado en el Node a partir de $qrData).
     */
    public function enviarConQr(string $telefono, string $mensaje, string $qrData): bool
    {
        return $this->enviar($telefono, $mensaje, $qrData);
    }

    /**
     * Envía un mensaje de solo texto (p. ej. el recordatorio de vencimiento).
     */
    public function enviarTexto(string $telefono, string $mensaje): bool
    {
        return $this->enviar($telefono, $mensaje, null);
    }

    /**
     * Llama al microservicio. Nunca lanza excepción hacia arriba: si falla, lo
     * registra y devuelve false (para no afectar el alta ni el comando).
     */
    private function enviar(string $telefono, string $mensaje, ?string $qrData): bool
    {
        if (! $this->habilitado()) {
            return false;
        }

        $destino = $this->normalizarTelefono($telefono);
        if ($destino === null) {
            Log::warning('WhatsApp: teléfono inválido, no se envió.', ['telefono' => $telefono]);
            return false;
        }

        $payload = ['to' => $destino, 'message' => $mensaje];
        if ($qrData !== null) {
            $payload['qr'] = $qrData;
        }

        try {
            $respuesta = Http::withToken((string) config('services.whatsapp.token'))
                ->timeout(15)
                ->acceptJson()
                ->post(rtrim((string) config('services.whatsapp.url'), '/') . '/send', $payload);

            if ($respuesta->failed()) {
                Log::warning('WhatsApp: el microservicio respondió con error.', [
                    'status' => $respuesta->status(),
                    'body' => $respuesta->body(),
                ]);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::warning('WhatsApp: no se pudo contactar el microservicio.', ['error' => $e->getMessage()]);
            return false;
        }
    }
}

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #{{ $data['folio_pago'] }}</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Estilos específicos para impresoras térmicas de 80mm */
        @media print {
            body { width: 80mm; margin: 0; padding: 0; }
            .no-print { display: none !important; }
        }
        body { font-family: 'Courier New', Courier, monospace; color: #000; background-color: #f3f4f6; }
        .ticket { width: 80mm; margin: 20px auto; background: #fff; padding: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .divider { border-top: 1px dashed #000; margin: 10px 0; }
    </style>
</head>
<body>

    <!-- Botones de Acción (Se ocultan al imprimir) -->
    <div class="no-print text-center pt-8 pb-4 space-x-4">
        <a href="{{ route('socios.show', $member->id) }}" class="inline-block px-4 py-2 bg-gray-600 text-white font-bold text-sm rounded hover:bg-gray-700">Volver al Perfil</a>
        <button onclick="window.print()" class="inline-block px-4 py-2 bg-orange-500 text-white font-bold text-sm rounded hover:bg-orange-600">Reimprimir Ticket</button>
    </div>

    <!-- Contenido del Ticket -->
    <div class="ticket">
        <div class="text-center mb-4">
            <h1 class="text-2xl font-bold">OLLINFIT GYM</h1>
            <p class="text-xs">Valle de Chalco, Estado de México</p>
        </div>

        <div class="divider"></div>
        
        <div class="text-xs space-y-1 mb-4">
            <p><strong>Fecha:</strong> {{ $fecha }}</p>
            <p><strong>Cajero:</strong> ADMIN (Turno 1)</p>
            <p><strong>Folio TXN:</strong> {{ $data['folio_pago'] }}</p>
        </div>

        <div class="divider"></div>

        <div class="text-sm space-y-2 mb-4">
            <p><strong>Socio:</strong> {{ $member->name }} {{ $member->last_name }}</p>
            <p><strong>Folio:</strong> #{{ $member->folio }}</p>
        </div>

        <div class="divider"></div>

        <table class="w-full text-sm mb-4">
            <tr>
                <td class="pb-2"><strong>Concepto</strong></td>
                <td class="text-right pb-2"><strong>Importe</strong></td>
            </tr>
            <tr>
                <td>Renovación: {{ $data['plan_name'] }}</td>
                <td class="text-right">${{ number_format($data['amount'], 2) }}</td>
            </tr>
        </table>

        <div class="divider"></div>

        <div class="flex justify-between font-bold text-base mb-2">
            <span>TOTAL:</span>
            <span>${{ number_format($data['amount'], 2) }} MXN</span>
        </div>
        
        <div class="text-xs mb-4">
            <p><strong>Método de pago:</strong> {{ strtoupper($data['method']) }}</p>
            @if($data['method'] == 'tarjeta')
                <p><strong>Aprobación:</strong> AUT-{{ rand(1000, 9999) }}</p>
            @endif
        </div>

        <div class="divider"></div>

        <div class="text-center text-xs mt-4 space-y-1">
            <p>¡Gracias por tu preferencia!</p>
            <p>Este ticket es tu comprobante de pago.</p>
            <p><strong>Nueva Vigencia:</strong> {{ $member->expiration_date->format('d/m/Y') }}</p>
        </div>
    </div>

    <!-- Script para imprimir automáticamente -->
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
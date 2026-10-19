<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reimpresión Ticket #MX-{{ $sale->id }}</title>
    <!-- Asegúrate de cargar Tailwind aquí (asumiendo que usas Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        @media print {
            body * {
                visibility: hidden !important;
            }
            #ticket-receipt,
            #ticket-receipt * {
                visibility: visible !important;
            }
            #ticket-receipt {
                position: absolute !important;
                left: 50% !important;
                top: 0 !important;
                transform: translateX(-50%) !important;
                width: 80mm !important; /* Formato miniprinter */
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                background: white !important;
            }
            .print\:hidden {
                display: none !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">

    <!-- CONTENEDOR DEL TICKET (Tu diseño exacto) -->
    <div id="ticket-receipt" class="bg-white w-full max-w-sm rounded-3xl shadow-xl overflow-hidden flex flex-col p-8 space-y-5 border border-gray-200">
        
        <!-- Encabezado del Ticket -->
        <div class="flex justify-between items-start border-b border-gray-100 pb-4">
            <div class="space-y-0.5">
                <h3 class="font-black text-slate-900 text-lg uppercase tracking-wider">OllinFit</h3>
                <p class="text-[11px] font-semibold text-orange-600 uppercase tracking-widest">Centro de Acceso y Tienda</p>
                <p class="text-[10px] font-bold text-gray-400 mt-1">* REIMPRESIÓN *</p>
            </div>
            <button onclick="window.close()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-rose-500 hover:text-white text-gray-500 font-bold flex items-center justify-center transition-colors cursor-pointer print:hidden">✕</button>
        </div>

        <!-- Datos generales -->
        <div class="space-y-1.5 text-xs text-gray-500 border-b border-dashed border-gray-200 pb-4">
            <div class="flex justify-between"><span>Folio:</span> <strong class="text-slate-800">#MX-{{ $sale->id }}</strong></div>
            <div class="flex justify-between"><span>Fecha:</span> <strong class="text-slate-800">{{ $sale->created_at->format('d/m/Y H:i') }}</strong></div>
            <div class="flex justify-between"><span>Método de Pago:</span> <strong class="text-slate-800">{{ $sale->payment_method ?? 'Efectivo' }}</strong></div>
        </div>

        <!-- Lista de productos -->
        <div class="border-b border-dashed border-gray-200 py-3 space-y-2.5 max-h-64 overflow-y-auto">
            @foreach($items as $item)
                <div class="flex justify-between items-center text-xs">
                    <div>
                        <span class="font-bold text-slate-800 block">{{ $item->product_name }}</span>
                        <span class="text-[11px] text-gray-400">{{ $item->quantity }} x ${{ number_format($item->price, 2) }}</span>
                    </div>
                    <span class="font-extrabold text-slate-900">${{ number_format($item->price * $item->quantity, 2) }}</span>
                </div>
            @endforeach
        </div>

        <!-- Totales -->
        <div class="space-y-2 text-xs pt-1">
            <div class="flex justify-between font-black text-base text-slate-900 border-t border-gray-100 pt-3">
                <span>TOTAL:</span>
                <span class="text-emerald-600">${{ number_format($sale->total, 2) }}</span>
            </div>
            
            @if(($sale->payment_method ?? 'Efectivo') === 'Efectivo')
                <!-- Opcional: Si guardas el monto recibido y el cambio en la base de datos, cámbialo aquí. Si no, puedes omitir esta cajita en la reimpresión -->
                <div class="space-y-1 pt-1 text-gray-500 bg-gray-50 p-3 rounded-2xl">
                    <p class="text-center text-[10px] font-semibold text-gray-400">Pago en efectivo procesado.</p>
                </div>
            @endif
        </div>

        <!-- Pie de página -->
        <div class="text-center pt-2 border-t border-dashed border-gray-200 space-y-1">
            <p class="text-xs font-bold text-slate-700">¡Gracias por tu visita!</p>
            <p class="text-[10px] text-gray-400">Conserva este ticket para cualquier aclaración.</p>
        </div>

        <!-- Botones de acción (Ocultos al imprimir) -->
        <div class="flex gap-2 pt-2 print:hidden">
            <button onclick="window.print()" class="flex-1 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs py-3 rounded-2xl transition-colors cursor-pointer shadow-md flex items-center justify-center gap-2">
                🖨️ Imprimir
            </button>
            <button onclick="window.close()" class="flex-1 bg-gray-200 hover:bg-gray-300 text-slate-700 font-bold text-xs py-3 rounded-2xl transition-colors cursor-pointer shadow-md flex items-center justify-center gap-2">
                Cerrar Pestaña
            </button>
        </div>
    </div>

    <!-- Script para lanzar la impresión automáticamente -->
    <script>
        window.onload = function() {
            setTimeout(() => {
                window.print();
            }, 500); // Pequeño retraso para asegurar que los estilos de Tailwind carguen
        }
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gafete — {{ $member->name }} {{ $member->last_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #f3f4f6; font-family: ui-sans-serif, system-ui, sans-serif; }
        .gafete {
            width: 320px;
            margin: 24px auto;
            background: #fff;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,.15);
        }
        @media print {
            body { background: #fff; }
            .no-print { display: none !important; }
            .gafete { box-shadow: none; margin: 0 auto; border: 1px solid #e5e7eb; }
            @page { margin: 12mm; }
        }
    </style>
</head>
<body>

    <!-- Botones (no se imprimen) -->
    <div class="no-print text-center pt-8 pb-2 space-x-3">
        <a href="{{ route('socios.show', $member->id) }}" class="inline-block px-4 py-2 bg-gray-600 text-white font-bold text-sm rounded-lg hover:bg-gray-700">Volver al perfil</a>
        <button onclick="window.print()" class="inline-block px-4 py-2 bg-orange-500 text-white font-bold text-sm rounded-lg hover:bg-orange-600">Imprimir gafete</button>
    </div>

    <!-- Credencial -->
    <div class="gafete">
        <!-- Encabezado -->
        <div class="bg-zinc-900 px-6 py-5 text-center">
            <h1 class="text-xl font-extrabold text-white tracking-wide">OLLINFIT <span class="text-orange-500">GYM</span></h1>
            <p class="text-[10px] text-zinc-400 uppercase tracking-widest mt-0.5">Gafete Digital de Acceso</p>
        </div>

        <!-- Foto + datos -->
        <div class="px-6 pt-6 text-center">
            <div class="w-24 h-24 mx-auto rounded-full border-4 border-gray-100 bg-gray-200 overflow-hidden shadow-sm flex items-center justify-center">
                @if($member->profile_photo_path)
                    <img src="{{ asset('storage/' . $member->profile_photo_path) }}" alt="{{ $member->name }}" class="w-full h-full object-cover">
                @else
                    <span class="text-2xl font-bold text-gray-500">{{ substr($member->name, 0, 1) }}{{ substr($member->last_name, 0, 1) }}</span>
                @endif
            </div>

            <h2 class="text-lg font-bold text-gray-800 mt-3 leading-tight">{{ $member->name }} {{ $member->last_name }}</h2>
            <p class="text-xs text-gray-500 mt-0.5">Folio <span class="font-bold text-gray-700">#{{ $member->folio }}</span></p>

            <div class="mt-2 inline-flex items-center gap-1.5 bg-orange-50 text-orange-600 text-[11px] font-bold px-3 py-1 rounded-full">
                {{ $member->plan->name ?? 'Sin plan' }}
            </div>
        </div>

        <!-- QR -->
        <div class="px-6 py-6 text-center">
            <div class="inline-block p-3 border-2 border-dashed border-gray-200 rounded-xl bg-white">
                {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(170)->margin(1)->generate($member->folio) !!}
            </div>
            <p class="text-[11px] text-gray-400 mt-3 px-2 leading-relaxed">Muestra este código en recepción para registrar tu entrada.</p>
        </div>

        <!-- Pie -->
        <div class="bg-gray-50 border-t border-gray-100 px-6 py-3 text-center">
            <p class="text-[10px] text-gray-400">
                @if($member->expiration_date)
                    Vigente hasta el {{ $member->expiration_date->format('d/m/Y') }}
                @else
                    Membresía sin vigencia registrada
                @endif
            </p>
        </div>
    </div>

</body>
</html>

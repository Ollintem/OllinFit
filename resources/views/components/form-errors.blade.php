{{-- Muestra el mensaje flash de error y los errores de validación, para que
     ningún formulario falle "en silencio". Uso: <x-form-errors /> dentro del form. --}}
@if (session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 mb-4">
        {{ session('error') }}
    </div>
@endif
@if ($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 mb-4">
        <p class="font-bold mb-1">Revisa estos datos:</p>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

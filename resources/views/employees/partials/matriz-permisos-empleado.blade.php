@php
    // Estado de permisos de ESTE empleado.
    $delRol    = $empleado->getPermissionsViaRoles()->pluck('name')->all();
    $directos  = $empleado->getDirectPermissions()->pluck('name')->all();
    $denegados = $empleado->deniedPermissions->pluck('name')->all();

    // Permiso efectivo = (del rol ∪ directo) − denegado.
    $esEfectivo = function ($perm) use ($delRol, $directos, $denegados) {
        if (in_array($perm, $denegados)) {
            return false;
        }
        return in_array($perm, $delRol) || in_array($perm, $directos);
    };

    // Lista de los que están activos hoy (default de la matriz si no hay 'old').
    $efectivos = [];
    foreach (config('permisos') as $m) {
        if (! empty($m['solo_admin'])) {
            continue;
        }
        foreach ($m['permisos'] as $p => $e) {
            if ($esEfectivo($p)) {
                $efectivos[] = $p;
            }
        }
    }
@endphp

<div class="space-y-5">
    <p class="text-[13px] text-gray-500">
        Ajusta los accesos de <span class="font-semibold text-gray-700">este empleado en particular</span>.
        Los permisos marcados con <span class="text-[10px] font-bold uppercase tracking-wider text-blue-700 bg-blue-100 rounded-full px-1.5 py-0.5">rol</span>
        vienen de su rol: si lo desmarcas, se le quita solo a él (sin afectar a los demás). Lo que actives de más se le concede solo a él.
    </p>

    @foreach (config('permisos') as $modulo)
        @continue(! empty($modulo['solo_admin']))
        <div class="border border-gray-200 rounded-xl overflow-hidden">
            <div class="bg-gray-50 px-5 py-3 border-b border-gray-100">
                <h3 class="text-sm font-bold text-gray-800">{{ $modulo['label'] }}</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 p-5">
                @foreach ($modulo['permisos'] as $permiso => $etiqueta)
                    <label class="flex items-center gap-3 p-2.5 border border-gray-100 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                        <span class="relative inline-flex items-center shrink-0">
                            <input type="checkbox" name="permissions[]" value="{{ $permiso }}" class="sr-only peer"
                                {{ in_array($permiso, old('permissions', $efectivos)) ? 'checked' : '' }}>
                            <span class="w-9 h-5 bg-gray-200 rounded-full peer peer-checked:bg-orange-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:border-gray-300 after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-full peer-checked:after:border-white"></span>
                        </span>
                        <span class="text-sm font-medium text-gray-700 flex items-center gap-1.5">
                            {{ $etiqueta }}
                            @if (in_array($permiso, $delRol))
                                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-700 bg-blue-100 rounded-full px-1.5 py-0.5">rol</span>
                            @endif
                        </span>
                    </label>
                @endforeach
            </div>
        </div>
    @endforeach
</div>

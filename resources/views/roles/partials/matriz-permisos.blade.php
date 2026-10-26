@php
    // $role puede ser null (crear) o un Role (editar).
    $role = $role ?? null;
    $asignados = $role ? $role->permissions->pluck('name')->all() : [];
@endphp

<div class="space-y-5">
    @foreach (config('permisos') as $modulo)
        <div class="border border-gray-200 rounded-xl overflow-hidden">
            <div class="flex items-center justify-between gap-2 bg-gray-50 px-5 py-3 border-b border-gray-100">
                <h3 class="text-sm font-bold text-gray-800">{{ $modulo['label'] }}</h3>
                @if (! empty($modulo['solo_admin']))
                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700 bg-amber-100 rounded-full px-2 py-0.5">Solo Administrador</span>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 p-5">
                @foreach ($modulo['permisos'] as $permiso => $etiqueta)
                    <label class="flex items-center gap-3 p-2.5 border border-gray-100 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                        <span class="relative inline-flex items-center shrink-0">
                            <input type="checkbox" name="permissions[]" value="{{ $permiso }}" class="sr-only peer"
                                {{ in_array($permiso, old('permissions', $asignados)) ? 'checked' : '' }}>
                            <span class="w-9 h-5 bg-gray-200 rounded-full peer peer-checked:bg-orange-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:border-gray-300 after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-full peer-checked:after:border-white"></span>
                        </span>
                        <span class="text-sm font-medium text-gray-700">{{ $etiqueta }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    @endforeach
</div>

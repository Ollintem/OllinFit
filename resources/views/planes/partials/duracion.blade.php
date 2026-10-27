@php
    // Presets: etiqueta => [value, unit]
    $presets = [
        'pase'       => ['Pase de 1 día',  1, 'days'],
        'mensual'    => ['Mensual (1 mes)', 1, 'months'],
        'bimestral'  => ['Bimestral (2 meses)', 2, 'months'],
        'trimestral' => ['Trimestral (3 meses)', 3, 'months'],
        'semestral'  => ['Semestral (6 meses)', 6, 'months'],
        'anual'      => ['Anual (1 año)', 1, 'years'],
    ];
    // ¿El value/unit actual coincide con algún preset?
    $presetActual = 'custom';
    foreach ($presets as $key => [$label, $v, $u]) {
        if ((int) $value === $v && $unit === $u) { $presetActual = $key; break; }
    }
@endphp

<div x-data="{
        preset: '{{ $presetActual }}',
        value: {{ (int) $value }},
        unit: '{{ $unit }}',
        presets: {
            pase:       { value: 1, unit: 'days' },
            mensual:    { value: 1, unit: 'months' },
            bimestral:  { value: 2, unit: 'months' },
            trimestral: { value: 3, unit: 'months' },
            semestral:  { value: 6, unit: 'months' },
            anual:      { value: 1, unit: 'years' },
        },
        aplicarPreset() {
            if (this.preset !== 'custom') {
                this.value = this.presets[this.preset].value;
                this.unit = this.presets[this.preset].unit;
            }
        }
     }">
    <label class="block text-sm font-bold text-gray-700 mb-1">Duración <span class="text-red-500">*</span></label>

    <select x-model="preset" @change="aplicarPreset()"
            class="w-full border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500">
        @foreach ($presets as $key => [$label, $v, $u])
            <option value="{{ $key }}">{{ $label }}</option>
        @endforeach
        <option value="custom">Personalizado…</option>
    </select>

    <!-- Personalizado: cantidad + unidad -->
    <div x-show="preset === 'custom'" x-cloak class="mt-3 flex gap-3">
        <input type="number" min="1" x-model.number="value"
               class="w-1/2 border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500" placeholder="Ej. 45">
        <select x-model="unit"
                class="w-1/2 border-gray-200 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500">
            <option value="days">Días</option>
            <option value="months">Meses</option>
            <option value="years">Años</option>
        </select>
    </div>

    <!-- Lo que realmente se envía -->
    <input type="hidden" name="duration_value" :value="value">
    <input type="hidden" name="duration_unit" :value="unit">

    <p class="text-[11px] text-gray-400 mt-1">
        Elige un preset o "Personalizado". Los meses/años se calculan por calendario real.
    </p>
    @error('duration_value') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    @error('duration_unit') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
</div>

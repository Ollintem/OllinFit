<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'description',
        'duration_value',
        'duration_unit',
        'highlight_text',
        'color',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'duration_value' => 'integer',
    ];

    public function members()
    {
        // Un plan tiene muchos socios
        return $this->hasMany(Member::class);
    }

    /**
     * Calcula la vigencia real a partir de una fecha base, respetando el calendario.
     * NoOverflow evita que, p. ej., 31-ene + 1 mes se brinque a marzo (cae 28/29-feb).
     */
    public function extend(Carbon $fecha): Carbon
    {
        $base = $fecha->copy();
        $valor = max((int) $this->duration_value, 1);

        return match ($this->duration_unit) {
            'months' => $base->addMonthsNoOverflow($valor),
            'years' => $base->addYearsNoOverflow($valor),
            default => $base->addDays($valor),
        };
    }

    /**
     * Duración aproximada en días (solo para vistas previas y la barra de progreso).
     * La vigencia real siempre se calcula con extend().
     */
    public function getDurationDaysAttribute(): int
    {
        $valor = max((int) $this->duration_value, 1);

        return match ($this->duration_unit) {
            'months' => $valor * 30,
            'years' => $valor * 365,
            default => $valor,
        };
    }

    /**
     * Texto legible de la duración, p. ej. "1 mes", "6 meses", "1 año", "30 días".
     */
    public function duracionLabel(): string
    {
        $valor = max((int) $this->duration_value, 1);

        [$singular, $plural] = match ($this->duration_unit) {
            'months' => ['mes', 'meses'],
            'years' => ['año', 'años'],
            default => ['día', 'días'],
        };

        return $valor . ' ' . ($valor === 1 ? $singular : $plural);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashRegister extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'opening_amount',
        'closing_amount',
        'expected_amount',
        'difference',
        'status',
        'opened_at',
        'closed_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * La caja/turno abierto al que se deben ligar los cobros.
     * Hoy hay una sola caja abierta a la vez; si se recibe un usuario, se prefiere
     * la suya (preparado para cajas simultáneas por cajero en el futuro).
     */
    public static function abierta(?int $userId = null): ?self
    {
        $query = static::where('status', 'abierta');

        if ($userId !== null) {
            $propia = (clone $query)->where('user_id', $userId)->latest()->first();
            if ($propia) {
                return $propia;
            }
        }

        return $query->latest()->first();
    }
}
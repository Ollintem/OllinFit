<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Member extends Model
{
    // Los campos que permitimos guardar masivamente
    protected $fillable = [
        'folio',
         'name', 
         'last_name', 
         'email', 
         'phone', 
        'profile_photo_path', 
        'plan_id', 
        'expiration_date', 
        'is_active',
        'is_inside'
    ];

    // Convertimos la fecha de la base de datos a un objeto Carbon automáticamente
    protected $casts = [
        'expiration_date' => 'date',
        'is_active' => 'boolean',
    ];

    // Relación: Un socio pertenece a un Plan
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    // EL TRUCO SENIOR: Atributo dinámico para el estado
    protected function status(): Attribute
    {
        return Attribute::make(
            get: function () {
                // Si lo suspendiste manualmente
                if (!$this->is_active) return 'Inactivo';
                
                // Si no tiene fecha de vencimiento registrada
                if (!$this->expiration_date) return 'Sin plan';

                $today = Carbon::today();
                $expiration = $this->expiration_date->startOfDay();

                // Si la fecha ya pasó
                if ($expiration->isPast()) return 'Vencido';

                // Si faltan 5 días o menos para que se venza
                if ($today->diffInDays($expiration) <= 5) return 'Por vencer';

                // Si todo está en orden
                return 'Activo';
            }
        );
    }
}
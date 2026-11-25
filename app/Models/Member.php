<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class Member extends Model
{
    use Notifiable; // permite enviar notificaciones (email) al socio

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
        'reminder_sent_for',
        'is_active',
        'is_inside'
    ];

    // El qr_token es secreto: no se asigna de forma masiva ni se expone en JSON.
    protected $hidden = [
        'qr_token',
    ];

    // Convertimos la fecha de la base de datos a un objeto Carbon automáticamente
    protected $casts = [
        'expiration_date' => 'date',
        'reminder_sent_for' => 'date',
        'is_active' => 'boolean',
    ];

    // Al crear un socio, se genera su token aleatorio para el QR de acceso.
    protected static function booted(): void
    {
        static::creating(function (Member $member) {
            if (empty($member->qr_token)) {
                $member->qr_token = Str::random(40);
            }
        });
    }

    /** Genera un token nuevo (invalida el QR anterior). */
    public function regenerarQrToken(): void
    {
        $this->forceFill(['qr_token' => Str::random(40)])->save();
    }

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
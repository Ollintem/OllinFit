<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    // Autorizamos estos campos para asignación masiva
    protected $fillable = [
        'type',
        'concept',
        'description',
        'amount',
        'payment_method',
        'supplier_id',
        'cash_register_id',
        'user_id'
    ];

    // (Opcional) Si ya tenías tus relaciones aquí abajo, déjalas intactas
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
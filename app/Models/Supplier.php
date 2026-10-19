<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    // Aquí le decimos a Laravel qué campos sí se pueden guardar masivamente
    protected $fillable = [
        'name',
        'contact_name',
        'phone',
        'email',
    ];
}
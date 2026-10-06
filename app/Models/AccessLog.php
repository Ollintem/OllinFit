<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessLog extends Model
{
    use HasFactory;

    // ¡Esta es la línea clave que Laravel estaba pidiendo!
    protected $fillable = [
        'member_id', 
        'access_method', 
        'status'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
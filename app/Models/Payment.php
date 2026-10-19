<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id', 'plan_name', 'amount', 'payment_method', 'folio_pago'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
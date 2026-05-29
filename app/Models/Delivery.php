<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    protected $fillable = [
        'order_id',
        'status',
        'processed_at',
        'cooked_at', 
        'sent_at',
        'completed_at',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
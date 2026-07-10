<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefundRequest extends Model
{
    protected $fillable = [
        'order_id', 'user_id', 'bank_name', 'account_number',
        'account_holder_name', 'status', 'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
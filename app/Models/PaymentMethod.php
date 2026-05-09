<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'code', 'name', 'type', 'account_number', 'account_name',
        'logo_icon', 'instructions', 'qris_image', 'is_active', 'sort_order',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public static function active()
    {
        return static::where('is_active', true)->orderBy('sort_order')->get();
    }

    public function isBankTransfer(): bool { return $this->type === 'bank_transfer'; }
    public function isQris(): bool         { return $this->type === 'qris'; }
    public function isCash(): bool         { return $this->type === 'cash'; }
}
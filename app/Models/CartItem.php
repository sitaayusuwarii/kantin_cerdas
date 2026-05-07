<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'menu_id',
        'quantity',
        'note',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    // item milik cart tertentu
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    // item terkait menu tertentu
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }
}
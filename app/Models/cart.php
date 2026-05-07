<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'status',
    ];

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    /**
     * Cart ini dimiliki oleh satu user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Cart ini memiliki banyak cart items.
     * Selalu eager load 'menu' saat mengakses items untuk mencegah N+1.
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    // =========================================================================
    // ACCESSORS
    // =========================================================================

    /**
     * Hitung total harga semua item di cart ini.
     * Akses: $cart->total_price
     *
     * PERHATIAN: Jika Anda memanggil ini di loop, pastikan items sudah di-eager load.
     * Gunakan: Cart::with('items')->get() bukan Cart::all()
     */
    public function getTotalPriceAttribute(): float
    {
        return (float) $this->items->sum('subtotal');
    }

    /**
     * Total jumlah item (untuk badge di navbar).
     * Akses: $cart->total_quantity
     */
    public function getTotalQuantityAttribute(): int
    {
        return (int) $this->items->sum('quantity');
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    /**
     * Cek apakah cart ini sedang aktif.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
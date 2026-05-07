<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Delivery extends Model
{
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'order_id',
        'driver_id',
        'delivery_type',
        'status',
        'address',
        'delivered_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'delivered_at' => 'datetime',
        ];
    }

    // =========================================================================
    // ACCESSORS
    // =========================================================================

    /**
     * Label tipe pengiriman. Akses: $delivery->type_label
     * Enum: pickup, delivery
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->delivery_type) {
            'pickup'   => 'Ambil Sendiri',
            'delivery' => 'Diantar',
            default    => ucfirst($this->delivery_type),
        };
    }

    /**
     * Label status pengiriman. Akses: $delivery->status_label
     * Enum: pending, on_delivery, delivered
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'     => 'Menunggu',
            'on_delivery' => 'Sedang Diantarkan',
            'delivered'   => 'Sudah Diterima',
            default       => ucfirst($this->status),
        };
    }

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Pengemudi/pengelola yang bertanggung jawab atas pengiriman ini.
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}
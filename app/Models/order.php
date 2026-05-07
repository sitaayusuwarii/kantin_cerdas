<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'order_number',
        'total_price',
        'status',
        'pickup_schedule',
        'note',
        'ordered_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_price' => 'decimal:2',
            'ordered_at'  => 'datetime',
        ];
    }

    // =========================================================================
    // ACCESSORS
    // =========================================================================

    /**
     * Format total_price ke Rupiah. Akses: $order->formatted_total
     */
    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->total_price, 0, ',', '.');
    }

    /**
     * Label status dalam Bahasa Indonesia. Akses: $order->status_label
     * Enum: baru, diproses, selesai, dibatalkan
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'baru'        => 'Pesanan Baru',
            'diproses'    => 'Sedang Diproses',
            'selesai'     => 'Selesai',
            'dibatalkan'  => 'Dibatalkan',
            default       => ucfirst($this->status),
        };
    }

    /**
     * Warna badge untuk UI. Akses: $order->status_color
     * Nilai: Bootstrap/Tailwind color class.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'baru'       => 'warning',
            'diproses'   => 'info',
            'selesai'    => 'success',
            'dibatalkan' => 'danger',
            default      => 'secondary',
        };
    }

    /**
     * Label jadwal pengambilan. Akses: $order->pickup_schedule_label
     * Enum: istirahat_1, istirahat_2, pulang
     */
    public function getPickupScheduleLabelAttribute(): ?string
    {
        if (! $this->pickup_schedule) {
            return null;
        }

        return match ($this->pickup_schedule) {
            'istirahat_1' => 'Istirahat 1 (09:30)',
            'istirahat_2' => 'Istirahat 2 (11:30)',
            'pulang'      => 'Pulang Sekolah (15:00)',
            default       => $this->pickup_schedule,
        };
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    /**
     * Filter berdasarkan status.
     * Penggunaan: Order::withStatus('baru')->get()
     */
    public function scopeWithStatus(Builder $query, ?string $status): Builder
    {
        $validStatuses = ['baru', 'diproses', 'selesai', 'dibatalkan'];

        return $query->when(
            filled($status) && in_array($status, $validStatuses, strict: true),
            fn (Builder $q) => $q->where('status', $status)
        );
    }

    /**
     * Order terbaru di atas.
     */
    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderByDesc('ordered_at');
    }

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    /**
     * Order ini dimiliki oleh satu user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Order ini memiliki banyak order items.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Order ini memiliki satu payment.
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Order ini memiliki satu delivery record.
     */
    public function delivery(): HasOne
    {
        return $this->hasOne(Delivery::class);
    }
}
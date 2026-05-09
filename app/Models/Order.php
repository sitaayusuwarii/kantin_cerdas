<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

   protected $fillable = [
    'user_id',
    'order_number',
    'status',
    'pickup_schedule',
    'note',
    'total_price',
    'confirmed_at',
    'processed_at',
    'shipped_at',
    'completed_at',
    'cancelled_at',
    'confirmed_by',
    'payment_method',   
    'payment_proof',    
    'payment_status',  
];

    protected function casts(): array
    {
        return [
            'total_price'  => 'decimal:2',
            'confirmed_at' => 'datetime',
            'processed_at' => 'datetime',
            'shipped_at'   => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    // ─── Konstanta ────────────────────────────────────────

    const STATUS_BARU          = 'baru';
    const STATUS_DIKONFIRMASI  = 'dikonfirmasi';
    const STATUS_DIPROSES      = 'diproses';
    const STATUS_DIKIRIM       = 'dikirim';
    const STATUS_SELESAI       = 'selesai';
    const STATUS_DIBATALKAN    = 'dibatalkan';

    const PICKUP_ISTIRAHAT_1   = 'istirahat_1';
    const PICKUP_ISTIRAHAT_2   = 'istirahat_2';
    const PICKUP_PULANG        = 'pulang';

    // ─── Label untuk tampilan Blade ───────────────────────

    public static array $statusLabels = [
        'baru'         => 'Baru',
        'dikonfirmasi' => 'Dikonfirmasi',
        'diproses'     => 'Diproses',
        'dikirim'      => 'Dikirim',
        'selesai'      => 'Selesai',
        'dibatalkan'   => 'Dibatalkan',
    ];

    public static array $statusColors = [
        'baru'         => 'bg-blue-100 text-blue-700',
        'dikonfirmasi' => 'bg-amber-100 text-amber-700',
        'diproses'     => 'bg-orange-100 text-orange-700',
        'dikirim'      => 'bg-violet-100 text-violet-700',
        'selesai'      => 'bg-emerald-100 text-emerald-700',
        'dibatalkan'   => 'bg-red-100 text-red-600',
    ];

    public static array $pickupLabels = [
        'istirahat_1' => 'Istirahat 1 (09:30)',
        'istirahat_2' => 'Istirahat 2 (12:00)',
        'pulang'      => 'Pulang (14:30)',
    ];

    // ─── Accessors ────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return self::$statusLabels[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::$statusColors[$this->status] ?? 'bg-gray-100 text-gray-600';
    }

    public function getPickupLabelAttribute(): string
    {
        return self::$pickupLabels[$this->pickup_schedule] ?? $this->pickup_schedule;
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }

    // ─── Order Number Generator ───────────────────────────

    /**
     * Generate nomor order unik: SC-001, SC-002, dst.
     * Dipanggil dari OrderController saat create.
     */
    public static function generateOrderNumber(): string
    {
        $latest = self::withTrashed()
                      ->orderByDesc('id')
                      ->value('order_number');

        if (!$latest) {
            return 'SC-001';
        }

        $number = (int) substr($latest, 3); // ambil angka dari "SC-001"
        return 'SC-' . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
    }

    // ─── Scopes ───────────────────────────────────────────

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['selesai', 'dibatalkan']);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // ─── Status Helpers ───────────────────────────────────

    public function isBaru(): bool         { return $this->status === self::STATUS_BARU; }
    public function isDikonfirmasi(): bool { return $this->status === self::STATUS_DIKONFIRMASI; }
    public function isDiproses(): bool     { return $this->status === self::STATUS_DIPROSES; }
    public function isDikirim(): bool      { return $this->status === self::STATUS_DIKIRIM; }
    public function isSelesai(): bool      { return $this->status === self::STATUS_SELESAI; }
    public function isDibatalkan(): bool   { return $this->status === self::STATUS_DIBATALKAN; }

    /**
     * Status berikutnya dalam alur pengiriman
     */
    public function nextStatus(): ?string
    {
        return match ($this->status) {
            self::STATUS_BARU         => self::STATUS_DIKONFIRMASI,
            self::STATUS_DIKONFIRMASI => self::STATUS_DIPROSES,
            self::STATUS_DIPROSES     => self::STATUS_DIKIRIM,
            self::STATUS_DIKIRIM      => self::STATUS_SELESAI,
            default                   => null,
        };
    }

    // ─── Relasi ───────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function delivery()
{
    return $this->hasOne(Delivery::class);
}


}

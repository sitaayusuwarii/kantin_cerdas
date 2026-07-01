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
        'order_type',        // dine_in | takeaway | delivery
        'pickup_schedule',   // slot tetap: istirahat_1 | istirahat_2 | pulang (dine_in & delivery)
        'pickup_time',       // jam bebas "HH:MM" (takeaway saja)
        'classroom',         // kelas tujuan (delivery saja)
        'table_number',      // kolom lama, tidak dipakai tapi dibiarkan
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
         'order_source', 
         'kasir_id', 
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

    // ─── Konstanta Status ─────────────────────────────────

    const STATUS_BARU         = 'baru';
    const STATUS_DIKONFIRMASI = 'dikonfirmasi';
    const STATUS_DIPROSES     = 'diproses';
    const STATUS_DIKIRIM      = 'dikirim';
    const STATUS_SELESAI      = 'selesai';
    const STATUS_DIBATALKAN   = 'dibatalkan';
    const STATUS_SELESAI_DIMASAK = 'selesai_dimasak';

    // ─── Konstanta Pickup Slot ────────────────────────────

    const PICKUP_ISTIRAHAT_1 = 'istirahat_1';
    const PICKUP_ISTIRAHAT_2 = 'istirahat_2';
    const PICKUP_PULANG      = 'pulang';

    // ─── Konstanta Order Type ─────────────────────────────

    const ORDER_TYPE_DINEIN   = 'dine_in';
    const ORDER_TYPE_TAKEAWAY = 'takeaway';
    const ORDER_TYPE_DELIVERY = 'delivery';  // antar ke kelas

    // ─── Label & Warna ────────────────────────────────────

   public static array $statusLabels = [
    'baru'                     => 'Baru',
    'pembayaran_terverifikasi' => 'Baru',
    'dikonfirmasi'             => 'Dikonfirmasi',
    'diproses'                 => 'Diproses',
    'dikirim'                  => 'Dikirim',
    'selesai'                  => 'Selesai',
    'dibatalkan'               => 'Dibatalkan',
    'selesai_dimasak' => 'Selesai Dimasak',
];

    public static array $statusColors = [
    'baru'                     => 'bg-blue-100 text-blue-700',
    'pembayaran_terverifikasi' => 'bg-blue-100 text-blue-700',
    'dikonfirmasi'             => 'bg-amber-100 text-amber-700',
    'diproses'                 => 'bg-orange-100 text-orange-700',
    'dikirim'                  => 'bg-violet-100 text-violet-700',
    'selesai'                  => 'bg-emerald-100 text-emerald-700',
    'dibatalkan'               => 'bg-red-100 text-red-600',
    'selesai_dimasak' => 'bg-green-100 text-green-700',
];

    public static array $pickupLabels = [
        'istirahat_1' => 'Istirahat 1 (09:30)',
        'istirahat_2' => 'Istirahat 2 (12:00)',
        'pulang'      => 'Pulang (14:30)',
    ];

    public static array $orderTypeLabels = [
        'dine_in'  => 'Dine In',
        'takeaway' => 'Take Away',
        'delivery' => 'Antar ke Kelas',
    ];

    public static array $orderTypeColors = [
        'dine_in'  => 'bg-teal-100 text-teal-700',
        'takeaway' => 'bg-orange-100 text-orange-700',  // orange bukan purple
        'delivery' => 'bg-purple-100 text-purple-700',
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
        return self::$pickupLabels[$this->pickup_schedule] ?? ($this->pickup_schedule ?? '—');
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }

    public function getOrderTypeLabelAttribute(): string
    {
        return self::$orderTypeLabels[$this->order_type] ?? $this->order_type;
    }

    public function getOrderTypeColorAttribute(): string
    {
        return self::$orderTypeColors[$this->order_type] ?? 'bg-gray-100 text-gray-600';
    }

    /**
     * Waktu pickup siap pakai untuk tampilan di history, invoice, dashboard.
     * - takeaway  → "Jam 10:30"
     * - dine_in   → "Istirahat 1 (09:30)"
     * - delivery  → "Istirahat 2 (12:00)" + classroom
     */
    public function getPickupDisplayAttribute(): string
    {
        if ($this->order_type === self::ORDER_TYPE_TAKEAWAY) {
            return $this->pickup_time ? 'Jam ' . $this->pickup_time : '—';
        }
        return self::$pickupLabels[$this->pickup_schedule] ?? ($this->pickup_schedule ?? '—');
    }

    // ─── Order Number Generator ───────────────────────────

    public static function generateOrderNumber(): string
    {
        $latest = self::withTrashed()
                      ->orderByDesc('id')
                      ->value('order_number');

        if (!$latest) {
            return 'SC-001';
        }

        $number = (int) substr($latest, 3);
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

   public function isBaru(): bool {
    return in_array($this->status, [self::STATUS_BARU, 'pembayaran_terverifikasi']);}
    public function isDikonfirmasi(): bool { return $this->status === self::STATUS_DIKONFIRMASI; }
    public function isDiproses(): bool     { return $this->status === self::STATUS_DIPROSES; }
    public function isDikirim(): bool      { return $this->status === self::STATUS_DIKIRIM; }
    public function isSelesai(): bool      { return $this->status === self::STATUS_SELESAI; }
    public function isDibatalkan(): bool   { return $this->status === self::STATUS_DIBATALKAN; }

    // ─── Order Type Helpers ───────────────────────────────

    public function isDineIn(): bool   { return $this->order_type === self::ORDER_TYPE_DINEIN; }
    public function isTakeaway(): bool { return $this->order_type === self::ORDER_TYPE_TAKEAWAY; }
    public function isDelivery(): bool { return $this->order_type === self::ORDER_TYPE_DELIVERY; }

    // ─── Status Flow ─────────────────────────────────────

   public function nextStatus(): ?string
{
    return match ($this->status) {
        self::STATUS_BARU             => self::STATUS_DIKONFIRMASI,
        self::STATUS_DIKONFIRMASI     => self::STATUS_DIPROSES,
        self::STATUS_DIPROSES         => self::STATUS_SELESAI_DIMASAK,
        self::STATUS_SELESAI_DIMASAK  => $this->isDelivery()
                                            ? self::STATUS_DIKIRIM
                                            : self::STATUS_SELESAI,
        self::STATUS_DIKIRIM          => self::STATUS_SELESAI,
        default                       => null,
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

    public function delivery(): HasOne
    {
        return $this->hasOne(Delivery::class);
    }

    public function kasir()
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }

    // Helper: cek sumber order
    public function isFromKasir(): bool
    {
        return $this->order_source === 'kasir';
    }

    // Ambil order items dikelompokkan per tenant
    public function itemsPerTenant()
    {
        return $this->orderItems()
                    ->with(['menu', 'tenant'])
                    ->get()
                    ->groupBy('tenant_id');
    }

    public function getRouteKeyName()
{
    return 'order_number';
}
}
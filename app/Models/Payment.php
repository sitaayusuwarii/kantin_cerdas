<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'amount',
        'method',
        'proof_path',
        'status',
        'note',
        'rejection_reason',
        'verified_at',
        'verified_by',
        'payment_method',   
        'payment_proof',    
        'payment_status',
    ];

    protected function casts(): array
    {
        return [
            'amount'      => 'decimal:2',
            'verified_at' => 'datetime',
        ];
    }

    // ─── Konstanta ────────────────────────────────────────

    const STATUS_MENUNGGU      = 'menunggu';
    const STATUS_TERVERIFIKASI = 'terverifikasi';
    const STATUS_DITOLAK       = 'ditolak';

    public static array $methodLabels = [
        'transfer_bri'     => 'Transfer BRI',
        'transfer_bca'     => 'Transfer BCA',
        'transfer_mandiri' => 'Transfer Mandiri',
        'gopay'            => 'GoPay',
        'ovo'              => 'OVO',
        'dana'             => 'DANA',
        'tunai'            => 'Tunai',
    ];

    public static array $statusLabels = [
        'menunggu'      => 'Menunggu Verifikasi',
        'terverifikasi' => 'Terverifikasi',
        'ditolak'       => 'Ditolak',
    ];

    public static array $statusColors = [
        'menunggu'      => 'bg-yellow-100 text-yellow-700',
        'terverifikasi' => 'bg-emerald-100 text-emerald-700',
        'ditolak'       => 'bg-red-100 text-red-600',
    ];

    // ─── Accessors ────────────────────────────────────────

    public function getProofUrlAttribute(): ?string
    {
        if (!$this->proof_path) return null;

        if (Str::startsWith($this->proof_path, 'http')) {
            return $this->proof_path;
        }

        return Storage::url($this->proof_path);
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getMethodLabelAttribute(): string
    {
        return self::$methodLabels[$this->method] ?? $this->method;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::$statusLabels[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::$statusColors[$this->status] ?? 'bg-gray-100 text-gray-600';
    }

    // ─── Status Helpers ───────────────────────────────────

    public function isMenunggu(): bool      { return $this->status === self::STATUS_MENUNGGU; }
    public function isTerverifikasi(): bool { return $this->status === self::STATUS_TERVERIFIKASI; }
    public function isDitolak(): bool       { return $this->status === self::STATUS_DITOLAK; }

    // ─── Relasi ───────────────────────────────────────────

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}

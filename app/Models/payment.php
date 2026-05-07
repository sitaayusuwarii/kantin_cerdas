<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'order_id',
        'payment_method',
        'payment_proof',
        'amount',
        'status',
        'admin_note',
        'verified_by',
        'verified_at',
        'paid_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount'      => 'decimal:2',
            'verified_at' => 'datetime',
            'paid_at'     => 'datetime',
        ];
    }

    // =========================================================================
    // ACCESSORS
    // =========================================================================

    /**
     * Format jumlah pembayaran ke Rupiah. Akses: $payment->formatted_amount
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->amount, 0, ',', '.');
    }

    /**
     * URL publik bukti pembayaran. Akses: $payment->proof_url
     */
    public function getProofUrlAttribute(): ?string
    {
        if (empty($this->payment_proof)) {
            return null;
        }

        return \Storage::disk('public')->url($this->payment_proof);
    }

    /**
     * Label status pembayaran. Akses: $payment->status_label
     * Enum: pending, accepted, rejected
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'Menunggu Verifikasi',
            'accepted' => 'Diterima',
            'rejected' => 'Ditolak',
            default    => ucfirst($this->status),
        };
    }

    /**
     * Warna badge status. Akses: $payment->status_color
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'warning',
            'accepted' => 'success',
            'rejected' => 'danger',
            default    => 'secondary',
        };
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function hasProof(): bool
    {
        return ! empty($this->payment_proof);
    }

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Admin yang memverifikasi pembayaran ini.
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
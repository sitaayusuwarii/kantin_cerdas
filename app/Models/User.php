<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Override: Laravel default menggunakan 'email' untuk auth.
     * ERD kita menggunakan 'username', maka override di sini.
     * LoginRequest juga perlu disesuaikan (lihat catatan di AuthController).
     */
    public function getAuthIdentifierName(): string
    {
        return 'username';
    }

    /** @var list<string> */
    protected $fillable = [
        'username',
        'full_name',
        'phone',
        'class',
        'password',
        'role',
        'photo',
    ];

    /** @var list<string> */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    // =========================================================================
    // ROLE HELPERS
    // =========================================================================

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function isCustomer(): bool
    {
        return $this->hasRole('customer');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isPengelola(): bool
    {
        return $this->hasRole('pengelola');
    }

    // =========================================================================
    // ACCESSORS
    // =========================================================================

    /**
     * URL foto profil dengan fallback ke avatar placeholder.
     * Akses: $user->photo_url
     */
    public function getPhotoUrlAttribute(): string
    {
        if (! empty($this->photo) && \Storage::disk('public')->exists($this->photo)) {
            return \Storage::disk('public')->url($this->photo);
        }

        // Fallback: avatar berbasis inisial nama
        $initials = urlencode(substr($this->full_name, 0, 2));

        return "https://ui-avatars.com/api/?name={$initials}&background=random";
    }

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    /**
     * Menu yang difavoritkan oleh user ini.
     * Menggunakan tabel pivot 'favorites'.
     */
    public function favoriteMenus(): BelongsToMany
    {
        return $this->belongsToMany(
            related: Menu::class,
            table: 'favorites',
            foreignPivotKey: 'user_id',
            relatedPivotKey: 'menu_id',
        )->withTimestamps();
    }

    /**
     * Semua keranjang belanja milik user (termasuk yang sudah checkout).
     */
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Cart yang sedang aktif (hanya boleh ada satu).
     */
    public function activeCart(): HasOne
    {
        return $this->hasOne(Cart::class)->where('status', 'active');
    }

    /**
     * Semua order milik user.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
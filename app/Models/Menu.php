<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'image',
        'category',
        'is_available',
        'total_sold',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price'        => 'decimal:2',
            'stock'        => 'integer',
            'is_available' => 'boolean',
            'total_sold'   => 'integer',
        ];
    }

    // =========================================================================
    // ACCESSORS
    // =========================================================================

    /**
     * Format harga ke Rupiah. Akses: $menu->formatted_price
     * Contoh: 15000.00 → "Rp 15.000"
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->price, 0, ',', '.');
    }

    /**
     * URL gambar dengan fallback placeholder. Akses: $menu->image_url
     */
    public function getImageUrlAttribute(): string
    {
        if (! empty($this->image) && \Storage::disk('public')->exists($this->image)) {
            return \Storage::disk('public')->url($this->image);
        }

        return 'https://placehold.co/400x300/e2e8f0/64748b?text=' . urlencode($this->name);
    }

    /**
     * Cek apakah menu bisa dipesan (tersedia DAN stok > 0).
     * Akses: $menu->is_orderable
     */
    public function getIsOrderableAttribute(): bool
    {
        return $this->is_available && $this->stock > 0;
    }

    // =========================================================================
    // ELOQUENT SCOPES
    // =========================================================================

    /**
     * Filter menu yang bisa dipesan (available + stok ada).
     * Penggunaan: Menu::orderable()->get()
     */
    public function scopeOrderable(Builder $query): Builder
    {
        return $query->where('is_available', true)->where('stock', '>', 0);
    }

    /**
     * Filter berdasarkan keyword nama atau deskripsi.
     * Penggunaan: Menu::search('nasi')->get()
     */
    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        return $query->when(
            filled($keyword),
            fn (Builder $q) => $q->where(function (Builder $inner) use ($keyword): void {
                $inner->where('name', 'like', "%{$keyword}%")
                      ->orWhere('description', 'like', "%{$keyword}%");
            })
        );
    }

    /**
     * Filter berdasarkan kategori (string match).
     * Penggunaan: Menu::byCategory('Minuman')->get()
     */
    public function scopeByCategory(Builder $query, ?string $category): Builder
    {
        return $query->when(
            filled($category),
            fn (Builder $q) => $q->where('category', $category)
        );
    }

    /**
     * Sort berdasarkan harga.
     * Penggunaan: Menu::sortByPrice('asc')->get()
     */
    public function scopeSortByPrice(Builder $query, ?string $direction): Builder
    {
        $dir = in_array(strtolower((string) $direction), ['asc', 'desc'], strict: true)
            ? strtolower($direction)
            : 'asc';

        return $query->orderBy('price', $dir);
    }

    /**
     * Sort berdasarkan popularitas (total_sold desc).
     * Penggunaan: Menu::popular()->get()
     */
    public function scopePopular(Builder $query): Builder
    {
        return $query->orderByDesc('total_sold');
    }

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    /**
     * User yang memfavoritkan menu ini.
     */
    public function favoritedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(
            related: User::class,
            table: 'favorites',
            foreignPivotKey: 'menu_id',
            relatedPivotKey: 'user_id',
        )->withTimestamps();
    }

    /**
     * Cart items yang mereferensikan menu ini.
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Order items yang mereferensikan menu ini.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
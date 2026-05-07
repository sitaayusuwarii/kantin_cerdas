<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    /** @var list<string> */
    protected $fillable = ['name', 'slug'];

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    /**
     * Semua menu dalam kategori ini.
     */
    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    /**
     * Hanya menu yang sedang tersedia dalam kategori ini.
     */
    public function availableMenus(): HasMany
    {
        return $this->hasMany(Menu::class)->where('is_available', true);
    }
}
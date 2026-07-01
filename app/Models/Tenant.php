<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'logo',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Tenant punya 1 akun pengelola
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Tenant punya banyak menu
    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    // Tenant punya banyak order items
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
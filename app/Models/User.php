<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'full_name',
        'phone',
        'class',
        'password',
        'role',
        'photo',
        'telegram_chat_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function menus()
    {
        return $this->hasMany(Menu::class, 'seller_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function tenant()
    {
        return $this->hasOne(Tenant::class);
    }

    // Helper: cek role
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPengelola(): bool
    {
        return $this->role === 'pengelola';
    }

    public function isKasir(): bool
    {
        return $this->role === 'kasir';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

 }
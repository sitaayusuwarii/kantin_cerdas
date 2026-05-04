<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id', 'type', 'title', 'message',
        'icon', 'color', 'url', 'read_at'
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    // Helper static untuk bikin notif
    public static function send(int $userId, array $data): self
    {
        return self::create([
            'user_id' => $userId,
            ...$data,
        ]);
    }
}
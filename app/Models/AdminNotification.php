<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    protected $fillable = ['type', 'title', 'message', 'notifiable_type', 'notifiable_id', 'read_at'];

    protected $casts = ['read_at' => 'datetime'];

    public function notifiable()
    {
        return $this->morphTo();
    }

    public function isUnread(): bool
    {
        return is_null($this->read_at);
    }

    public function markAsRead(): void
    {
        $this->update(['read_at' => now()]);
    }
}
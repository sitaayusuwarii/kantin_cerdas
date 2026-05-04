<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    // Kirim ke semua pengelola
    public static function toAllPengelola(array $data): void
    {
        $pengelolas = User::where('role', 'pengelola')->pluck('id');
        foreach ($pengelolas as $id) {
            Notification::send($id, $data);
        }
    }

    public static function orderBaru(int $orderNumber, int $orderId): void
    {
        self::toAllPengelola([
            'type'    => 'order_new',
            'title'   => 'Pesanan Baru Masuk',
            'message' => "Order #{$orderNumber} menunggu konfirmasi.",
            'icon'    => 'fa-bag-shopping',
            'color'   => 'bg-forest-500',
            'url'     => '/pengelola/orders',
        ]);
    }

    public static function pembayaranBaru(int $orderNumber): void
    {
        self::toAllPengelola([
            'type'    => 'payment_new',
            'title'   => 'Bukti Pembayaran Masuk',
            'message' => "Order #{$orderNumber} mengirim bukti pembayaran.",
            'icon'    => 'fa-money-bill',
            'color'   => 'bg-amber-500',
            'url'     => '/pengelola/orders',
        ]);
    }

    public static function stokHabis(string $menuName): void
    {
        self::toAllPengelola([
            'type'    => 'stock_low',
            'title'   => 'Stok Hampir Habis',
            'message' => "Stok {$menuName} tersisa sedikit.",
            'icon'    => 'fa-triangle-exclamation',
            'color'   => 'bg-red-500',
            'url'     => '/pengelola/menu-management',
        ]);
    }
}
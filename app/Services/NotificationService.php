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

    public static function orderBaru($order, $user)
{
    if (!$user->telegram_chat_id) return;

    // Ambil item pesanan
    $order->load('items.menu');

    $text = "🧾 *Detail Pesanan*\n\n";
    $text .= "No Order: *{$order->order_number}*\n\n";

    foreach ($order->items as $item) {
        $text .= "🍽 {$item->menu->name}\n";
        $text .= "   {$item->quantity} x Rp " . number_format($item->unit_price,0,',','.') . "\n";
        $text .= "   Subtotal: Rp " . number_format($item->subtotal,0,',','.') . "\n\n";
    }

    $text .= "💰 *Total: Rp " . number_format($order->total_price,0,',','.') . "*\n\n";

    if ($order->note) {
        $text .= "📝 Catatan: {$order->note}\n\n";
    }

    $text .= "Silakan pilih metode pembayaran 👇";

    \Illuminate\Support\Facades\Http::post(
        "https://api.telegram.org/bot".env('TELEGRAM_BOT_TOKEN')."/sendMessage",
        [
            'chat_id' => $user->telegram_chat_id,
            'text' => $text,
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => '💳 QRIS', 'callback_data' => 'pay_qris'],
                        ['text' => '🏦 BCA', 'callback_data' => 'pay_bca'],
                        ['text' => '🏦 BRI', 'callback_data' => 'pay_bri'],
                    ]
                ]
            ])
        ]
    );
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
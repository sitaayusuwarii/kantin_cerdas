<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use Carbon\Carbon;

class SendPaymentReminder extends Command
{
    protected $signature   = 'reminder:payment';
    protected $description = 'Kirim reminder pembayaran & auto-cancel saat kantin tutup';

    public function handle()
    {
        $now         = Carbon::now();
        $kantinTutup = Carbon::today()->setTime(13, 0, 0);

        // ── AUTO CANCEL jam 13:00 ────────────────────────────────
        if ($now->between($kantinTutup, $kantinTutup->copy()->addMinutes(30))) {
            $expiredOrders = Order::where('status', 'baru')
                ->where('payment_status', 'pending')
                ->whereNull('payment_proof')
                ->whereDate('created_at', today())
                ->with('user')
                ->get();

            foreach ($expiredOrders as $order) {
                $order->update([
                    'status'       => 'dibatalkan',
                    'cancelled_at' => now(),
                ]);

                $chatId = $order->user->telegram_chat_id ?? null;
                if ($chatId) {
                    (new \App\Http\Controllers\TelegramController)->sendMessage(
                        $chatId,
                        "❌ *Pesanan dibatalkan otomatis.*\n\n" .
                        "Pesanan *#{$order->order_number}* dibatalkan karena kantin sudah tutup dan pembayaran belum diterima.\n\n" .
                        "Silakan pesan kembali besok ya! 🍽"
                    );
                }
            }

            $this->info('Auto-cancel selesai: ' . $expiredOrders->count() . ' pesanan dibatalkan.');
            return;
        }

        // ── REMINDER 1: 30 menit setelah order ───────────────────
        $orders30 = Order::where('status', 'baru')
            ->where('payment_status', 'pending')
            ->whereNull('payment_proof')
            ->whereBetween('created_at', [
                $now->copy()->subMinutes(60),
                $now->copy()->subMinutes(30),
            ])
            ->with('user')
            ->get();

        foreach ($orders30 as $order) {
            $chatId = $order->user->telegram_chat_id ?? null;
            if (!$chatId) continue;

            (new \App\Http\Controllers\TelegramController)->sendMessage(
                $chatId,
                "⏰ *Jangan lupa bayar pesananmu!*\n\n" .
                "Pesanan *#{$order->order_number}* senilai *Rp " . number_format($order->total_price, 0, ',', '.') . "* belum dibayar.\n\n" .
                "Pesanan akan dibatalkan otomatis jika kantin tutup jam 13:00. 🙏"
            );

            \App\Models\AdminNotification::create([
            'type'            => 'unpaid_reminder',
            'title'           => '⚠️ Pesanan Belum Dibayar',
            'message'         => "Pesanan #{$order->order_number} — {$order->user->full_name} belum melakukan pembayaran.",
            'notifiable_type' => \App\Models\Order::class,
            'notifiable_id'   => $order->id,
        ]);
        }

        // ── REMINDER 2: 1 jam sebelum kantin tutup ───────────────
        $oneHourBefore = $kantinTutup->copy()->subHour();

        if ($now->between($oneHourBefore, $oneHourBefore->copy()->addMinutes(30))) {
            $ordersMenjelangTutup = Order::where('status', 'baru')
                ->where('payment_status', 'pending')
                ->whereNull('payment_proof')
                ->whereDate('created_at', today())
                ->with('user')
                ->get();

            foreach ($ordersMenjelangTutup as $order) {
                $chatId = $order->user->telegram_chat_id ?? null;
                if (!$chatId) continue;

                (new \App\Http\Controllers\TelegramController)->sendMessage(
                    $chatId,
                    "🚨 *Peringatan Terakhir!*\n\n" .
                    "Pesanan *#{$order->order_number}* belum dibayar.\n\n" .
                    "Kantin tutup dalam *1 jam lagi* (13:00). Pesanan akan dibatalkan otomatis jika belum bayar! ⚠️"
                );
            }
            \App\Models\AdminNotification::create([
            'type'            => 'unpaid_reminder',
            'title'           => '⚠️ Pesanan Belum Dibayar',
            'message'         => "Pesanan #{$order->order_number} — {$order->user->full_name} belum melakukan pembayaran.",
            'notifiable_type' => \App\Models\Order::class,
            'notifiable_id'   => $order->id,
        ]);
        }

        $this->info('Reminder selesai dikirim.');
    }
}
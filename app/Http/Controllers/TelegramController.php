<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;


class TelegramController extends Controller
{
    public function handle(Request $request)
    {
        $data = $request->all();

        // =========================
        // SIMPAN CHAT ID
        // =========================
        if (isset($data['message'])) {
            $chatId = $data['message']['chat']['id'];
            $text   = $data['message']['text'];

            // format: /start 5
            if (str_starts_with($text, '/start')) {
                $parts = explode(' ', $text);
                $userId = $parts[1] ?? null;

                if ($userId) {
                    User::where('id', $userId)
                        ->update(['telegram_chat_id' => $chatId]);

                    Http::post("https://api.telegram.org/bot".env('8699640620:AAEElsnAHuwK8fh7G1oKLz37WEGy56UJTA8')."/sendMessage", [
                        'chat_id' => $chatId,
                        'text' => "✅ Telegram berhasil terhubung!"
                    ]);
                }
            }
        }

        // =========================
        // HANDLE FOTO BUKTI PEMBAYARAN
        // =========================
        if (isset($data['message']['photo'])) {

        $chatId = $data['message']['chat']['id'];

        $user = User::where('telegram_chat_id', $chatId)->first();
        if (!$user) return;

        $photo = end($data['message']['photo']);
        $fileId = $photo['file_id'];

        // ambil file dari telegram
        $file = Http::get("https://api.telegram.org/bot".env('TELEGRAM_BOT_TOKEN')."/getFile", [
            'file_id' => $fileId
        ]);

        $filePath = $file['result']['file_path'];
        $fileUrl = "https://api.telegram.org/file/bot".env('TELEGRAM_BOT_TOKEN')."/".$filePath;

        // ambil order terakhir
        $order = Order::where('user_id', $user->id)
            ->latest()
            ->first();

        if ($order) {
            $order->update([
                'payment_proof' => $fileUrl,
                'payment_status' => 'pending'
            ]);
        }

    Http::post("https://api.telegram.org/bot".env('TELEGRAM_BOT_TOKEN')."/sendMessage", [
        'chat_id' => $chatId,
        'text' => "✅ Bukti pembayaran berhasil dikirim.\nMenunggu verifikasi admin."
    ]);
}

        // =========================
        // HANDLE TOMBOL PEMBAYARAN
        // =========================
        if (isset($data['callback_query'])) {

        $callback = $data['callback_query'];
        $chatId = $callback['message']['chat']['id'];
        $action = $callback['data'];

        // ambil user dari chat_id
        $user = User::where('telegram_chat_id', $chatId)->first();

        if (!$user) return;

        $order = Order::where('user_id', $user->id)
            ->latest()
            ->first();

        if (!$order) return;

        // ========================
        // QRIS
        // ========================
        if ($action == 'pay_qris') {

            $order->update([
                'payment_method' => 'qris'
            ]);

            Http::post("https://api.telegram.org/bot".env('8699640620:AAEElsnAHuwK8fh7G1oKLz37WEGy56UJTA8')."/sendPhoto", [
                'chat_id' => $chatId,
                'photo' => 'https://via.placeholder.com/300x300.png?text=QRIS',
                'caption' => "Scan QRIS ini ya 💳\n\n📸 Setelah bayar, kirim bukti di sini"
            ]);
        }

        // ========================
        // BCA
        // ========================
        elseif ($action == 'pay_bca') {

            $order->update([
                'payment_method' => 'bca'
            ]);

            Http::post("https://api.telegram.org/bot".env('8699640620:AAEElsnAHuwK8fh7G1oKLz37WEGy56UJTA8')."/sendMessage", [
                'chat_id' => $chatId,
                'text' => "Transfer ke BCA:\n123456789 a.n Kantin\n\n📸 Kirim bukti transfer di sini ya"
            ]);
        }

        // ========================
        // BRI
        // ========================
        elseif ($action == 'pay_bri') {

            $order->update([
                'payment_method' => 'bri'
            ]);

            Http::post("https://api.telegram.org/bot".env('8699640620:AAEElsnAHuwK8fh7G1oKLz37WEGy56UJTA8')."/sendMessage", [
                'chat_id' => $chatId,
                'text' => "Transfer ke BRI:\n987654321 a.n Kantin\n\n📸 Kirim bukti transfer di sini ya"
            ]);
        }
    }
    }
}
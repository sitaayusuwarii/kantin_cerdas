<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\AdminNotification;

class TelegramController extends Controller
{
    private function botToken(): string
    {
        return env('TELEGRAM_BOT_TOKEN');
    }

    public function sendMessage(int|string $chatId, string $text, array $replyMarkup = []): void
    {
        $payload = [
            'chat_id'    => $chatId,
            'text'       => $text,
            'parse_mode' => 'Markdown',
        ];

        if (!empty($replyMarkup)) {
            $payload['reply_markup'] = json_encode($replyMarkup);
        }

        Http::post("https://api.telegram.org/bot{$this->botToken()}/sendMessage", $payload);
    }

    private function sendMainMenu(int|string $chatId): void
    {
        $this->sendMessage($chatId, "Halo! Selamat datang di Kantin 🍽\nSilakan pilih menu di bawah:", [
            'inline_keyboard' => [
                [
                    ['text' => '🍽 Pesan Makanan', 'callback_data' => 'go_menu'],
                ],
                // [
                //     ['text' => '📋 Tagihan Saya', 'callback_data' => 'go_tagihan'],
                // ],
                // [
                //     ['text' => '📦 Riwayat Pesanan', 'callback_data' => 'go_riwayat'],
                // ],
            ],
        ]);
    }

    public function sendTagihan(int|string $chatId, Order $order): void
    {
        $order->load('items.menu');

        $items = $order->items->map(function ($item) {
            return "• {$item->menu->name} x{$item->quantity} = Rp " . number_format($item->subtotal, 0, ',', '.');
        })->join("\n");

        $total = "Rp " . number_format($order->total_price, 0, ',', '.');

        $sudahPilihMetode = !empty($order->payment_method);

        if ($sudahPilihMetode) {
            // Sudah pilih metode di web → tampilkan rincian saja
            $method      = PaymentMethod::where('code', $order->payment_method)->first();
            $methodLabel = $method?->name ?? $order->payment_method;

            Http::post("https://api.telegram.org/bot{$this->botToken()}/sendMessage", [
                'chat_id'      => $chatId,
                'text'         => "🧾 *Tagihan Pesanan #{$order->order_number}*\n\n{$items}\n\n💰 *Total: {$total}*\n\n💳 Metode: *{$methodLabel}*\n\n📸 Silakan kirim foto bukti pembayaran di sini.",
                'parse_mode'   => 'Markdown',
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [['text' => '🔙 Kembali', 'callback_data' => 'go_main_menu']],
                    ],
                ]),
            ]);
        } else {
            // Belum pilih metode → tampilkan pilihan metode
            $methods = PaymentMethod::where('is_active', true)
                ->orderBy('sort_order')
                ->get();
            $buttons = $methods->map(function ($method) {
                $emoji = match($method->type) {
                    'bank_transfer' => '🏦',
                    'ewallet'       => '💸',
                    'qris'          => '💳',
                    'cash'          => '💵',
                    default         => '💰',
                };
                $label = $method->account_number
                    ? "{$emoji} {$method->name} ({$method->account_number})"
                    : "{$emoji} {$method->name}";
                return ['text' => $label, 'callback_data' => 'pay_' . $method->code];
            })->chunk(2)->map(fn($chunk) => $chunk->values()->toArray())->values()->toArray();

            $buttons[] = [['text' => '🔙 Kembali', 'callback_data' => 'go_main_menu']];

            Http::post("https://api.telegram.org/bot{$this->botToken()}/sendMessage", [
                'chat_id'      => $chatId,
                'text'         => "🧾 *Tagihan Pesanan #{$order->order_number}*\n\n{$items}\n\n💰 *Total: {$total}*\n\nPilih metode pembayaran:",
                'parse_mode'   => 'Markdown',
                'reply_markup' => json_encode(['inline_keyboard' => $buttons]),
            ]);
        }
    }

    public function handle(Request $request)
    {
        $data = $request->all();
        \Log::info('Telegram data:', $data);

        // =========================
        // HANDLE TEXT MESSAGE
        // =========================
        if (isset($data['message']['text'])) {
            $chatId = $data['message']['chat']['id'];
            $text   = trim($data['message']['text']);

            if (str_starts_with($text, '/start')) {
                $parts  = explode(' ', $text);
                $userId = $parts[1] ?? null;

                if ($userId) {
                    $updated = User::where('id', $userId)
                        ->update(['telegram_chat_id' => $chatId]);

                    if ($updated) {
                        $this->sendMessage($chatId, "✅ Akun berhasil terhubung!");
                    } else {
                        $this->sendMessage($chatId, "❌ User tidak ditemukan.");
                    }
                }

                $this->sendMainMenu($chatId);
                return response()->json(['ok' => true]);
            }

            if ($text === '/menu') {
                $user = User::where('telegram_chat_id', $chatId)->first();
                $this->handleGoMenu($chatId, $user);
                return response()->json(['ok' => true]);
            }

            if ($text === '/tagihan') {
                $user = User::where('telegram_chat_id', $chatId)->first();
                $this->handleGoTagihan($chatId, $user);
                return response()->json(['ok' => true]);
            }

            if ($text === '/riwayat') {
                $user = User::where('telegram_chat_id', $chatId)->first();
                $this->handleGoRiwayat($chatId, $user);
                return response()->json(['ok' => true]);
            }

            $this->sendMessage($chatId, "Ketik /menu untuk memesan makanan.");
        }

        // =========================
        // HANDLE FOTO BUKTI PEMBAYARAN
        // =========================
        if (isset($data['message']['photo'])) {
            $chatId = $data['message']['chat']['id'];
            $user   = User::where('telegram_chat_id', $chatId)->first();

            if (!$user) {
                $this->sendMessage($chatId, "❌ Akun kamu belum terhubung ke website.");
                return response()->json(['ok' => true]);
            }

            $photo    = end($data['message']['photo']);
            $fileId   = $photo['file_id'];
            $file     = Http::get("https://api.telegram.org/bot{$this->botToken()}/getFile", [
                'file_id' => $fileId,
            ]);
            $filePath = $file['result']['file_path'];
            $fileUrl  = "https://api.telegram.org/file/bot{$this->botToken()}/{$filePath}";

            $order = Order::where('user_id', $user->id)
                ->where('payment_status', 'pending')
                ->latest()
                ->first();

            \Log::info('Order ditemukan:', ['order' => $order?->toArray()]);

            if ($order) {
                if (!$order->payment_method) {
                    $this->sendMessage($chatId, "❌ Kamu belum memilih metode pembayaran.\nSilakan pilih metode bayar terlebih dahulu.");
                    $this->sendTagihan($chatId, $order);
                    return response()->json(['ok' => true]);
                }

                $order->update([
                    'payment_proof'  => $fileUrl,
                    'payment_status' => 'pending',
                ]);

                \App\Models\Payment::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'user_id'    => $user->id,
                        'amount'     => $order->total_price,
                        'method'     => $order->payment_method,
                        'proof_path' => $fileUrl,
                        'status'     => 'menunggu',
                    ]
                );

                $this->sendMessage($chatId, "✅ Bukti pembayaran berhasil dikirim.\nMenunggu verifikasi admin.");

                AdminNotification::create([
                    'type'            => 'payment_received',
                    'title'           => '💳 Bukti Pembayaran Masuk',
                    'message'         => "Pesanan #{$order->order_number} — {$user->full_name} mengirim bukti pembayaran.",
                    'notifiable_type' => Order::class,
                    'notifiable_id'   => $order->id,
                ]);

            } else {
                $this->sendMessage($chatId, "❌ Tidak ada pesanan aktif yang perlu dibayar.");
            }
        }

        // =========================
        // HANDLE CALLBACK QUERY
        // =========================
        if (isset($data['callback_query'])) {
            $callback = $data['callback_query'];
            $chatId   = $callback['message']['chat']['id'];
            $action   = $callback['data'];

            Http::post("https://api.telegram.org/bot{$this->botToken()}/answerCallbackQuery", [
                'callback_query_id' => $callback['id'],
            ]);

            $user = User::where('telegram_chat_id', $chatId)->first();

            if ($action === 'go_menu') {
                $this->handleGoMenu($chatId, $user);
                return response()->json(['ok' => true]);
            }

            if ($action === 'go_tagihan') {
                $this->handleGoTagihan($chatId, $user);
                return response()->json(['ok' => true]);
            }

            if ($action === 'go_riwayat') {
                $this->handleGoRiwayat($chatId, $user);
                return response()->json(['ok' => true]);
            }

            if ($action === 'go_main_menu') {
                $this->sendMainMenu($chatId);
                return response()->json(['ok' => true]);
            }

            // Pilihan metode pembayaran — dinamis dari database
            if (str_starts_with($action, 'pay_')) {
                if (!$user) {
                    $this->sendMessage($chatId, "❌ Akun belum terhubung.");
                    return response()->json(['ok' => true]);
                }

                $order = Order::where('user_id', $user->id)
                    ->where('payment_status', 'pending')
                    ->latest()
                    ->first();

                if (!$order) {
                    $this->sendMessage($chatId, "❌ Tidak ada pesanan aktif.");
                    return response()->json(['ok' => true]);
                }

                $methodCode = substr($action, 4); // hapus prefix 'pay_'
                $method     = PaymentMethod::where('code', $methodCode)
                    ->where('is_active', true)
                    ->first();

                if (!$method) {
                    $this->sendMessage($chatId, "❌ Metode pembayaran tidak tersedia.");
                    return response()->json(['ok' => true]);
                }

                $order->update(['payment_method' => $method->code]);

               if ($method->isQris()) {
    $imagePath = storage_path('app/public/' . $method->qris_image);
    
    if (file_exists($imagePath)) {
        Http::attach('photo', file_get_contents($imagePath), 'qris.jpg')
            ->post("https://api.telegram.org/bot{$this->botToken()}/sendPhoto", [
                'chat_id' => $chatId,
                'caption' => "Scan QRIS ini untuk membayar 💳\n\n📸 Setelah bayar, kirim foto bukti pembayaran di sini.",
            ]);
    } else {
        $this->sendMessage($chatId, "💳 *Pembayaran QRIS*\n\nSilakan scan QRIS di kasir.\n\n📸 Setelah bayar, kirim foto bukti di sini.");
    }



                } elseif ($method->isCash()) {
                    $this->sendMessage($chatId,
                        "💵 *Pembayaran Tunai*\n\n" .
                        "Silakan bayar langsung ke kasir kantin.\n\n" .
                        "💰 Total: Rp " . number_format($order->total_price, 0, ',', '.')
                    );
                } else {
                    $this->sendMessage($chatId,
                        "Transfer ke *{$method->name}*:\n`{$method->account_number}` a/n {$method->account_name}\n\n" .
                        "💰 Total: Rp " . number_format($order->total_price, 0, ',', '.') .
                        "\n\n📸 Setelah transfer, kirim foto bukti di sini."
                    );
                }

                return response()->json(['ok' => true]);
            }
        }

        return response()->json(['ok' => true]);
    }

    // =========================
    // PRIVATE HANDLER METHODS
    // =========================

    private function handleGoMenu(int|string $chatId, ?User $user): void
    {
        $link = config('app.url') . '/menu';

        Http::post("https://api.telegram.org/bot{$this->botToken()}/sendMessage", [
            'chat_id'      => $chatId,
            'text'         => "Klik tombol di bawah untuk membuka halaman menu 👇",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => '🍽 Buka Menu', 'url' => $link]],
                    [['text' => '🔙 Kembali', 'callback_data' => 'go_main_menu']],
                ],
            ]),
        ]);
    }

    private function handleGoTagihan(int|string $chatId, ?User $user): void
    {
        if (!$user) {
            $this->sendMessage($chatId, "❌ Akun kamu belum terhubung ke website.\nKlik link berikut untuk daftar: " . config('app.url') . '/register');
            return;
        }

        $order = Order::where('user_id', $user->id)
            ->where('payment_status', 'pending')
            ->whereNotIn('status', ['dibatalkan', 'selesai'])
            ->latest()
            ->first();

        if (!$order) {
            $adaOrder = Order::where('user_id', $user->id)->exists();

            $pesan = $adaOrder
                ? "✅ *Semua tagihan sudah lunas!*\n\nTidak ada tagihan yang perlu dibayar saat ini."
                : "📭 *Belum ada pesanan.*\n\nKamu belum pernah membuat pesanan.";

            $this->sendMessage($chatId, $pesan, [
                'inline_keyboard' => [
                    [['text' => '🍽 Pesan Sekarang', 'callback_data' => 'go_menu']],
                    [['text' => '🔙 Kembali', 'callback_data' => 'go_main_menu']],
                ],
            ]);
            return;
        }

        $this->sendTagihan($chatId, $order);
    }

    private function handleGoRiwayat(int|string $chatId, ?User $user): void
    {
        if (!$user) {
            $this->sendMessage($chatId, "❌ Akun kamu belum terhubung.");
            return;
        }

        $link = config('app.url') . '/history';

        Http::post("https://api.telegram.org/bot{$this->botToken()}/sendMessage", [
            'chat_id'      => $chatId,
            'text'         => "Lihat riwayat pesanan kamu di sini 👇",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => '📦 Buka Riwayat', 'url' => $link]],
                    [['text' => '🔙 Kembali', 'callback_data' => 'go_main_menu']],
                ],
            ]),
        ]);
    }
}
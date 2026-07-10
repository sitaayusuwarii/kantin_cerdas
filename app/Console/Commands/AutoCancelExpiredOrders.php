<?php

namespace App\Console\Commands;

use App\Http\Controllers\TelegramController;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class AutoCancelExpiredOrders extends Command
{
    protected $signature = 'orders:auto-cancel';
    protected $description = 'Batalkan otomatis pesanan yang belum diterima setelah semua tenant terkait tutup';

    public function handle(): void
    {
        $pendingOrders = Order::with(['user', 'items.menu'])
            ->whereIn('status', [Order::STATUS_BARU, 'pembayaran_terverifikasi'])
            ->get();

        if ($pendingOrders->isEmpty()) {
            $this->info('Tidak ada pesanan pending.');
            return;
        }

        // Ambil semua tenant sekaligus (hindari query berulang di loop)
        $tenants = Tenant::all()->keyBy('id');
        $now     = Carbon::now();
        $today   = Carbon::today();

        $cancelledCount = 0;

        foreach ($pendingOrders as $order) {
            $tenantIds = $order->items->pluck('tenant_id')->unique()->filter();

            if ($tenantIds->isEmpty()) {
                continue;
            }

            // Cek apakah SEMUA tenant yang terlibat sudah lewat jam tutup
            $allTenantsClosed = $tenantIds->every(function ($tenantId) use ($tenants, $today, $now) {
                $tenant = $tenants->get($tenantId);
                if (!$tenant || !$tenant->closing_time) {
                    return false; // kalau data tenant/jam tutup tidak ada, jangan batalkan (aman)
                }

                $cutoff = $today->copy()->setTimeFromTimeString($tenant->closing_time);
                return $now->greaterThanOrEqualTo($cutoff);
            });

            if (!$allTenantsClosed) {
                continue;
            }

            $this->cancelOrder($order, $tenants, $tenantIds);
            $cancelledCount++;
        }

        $this->info("Selesai. {$cancelledCount} pesanan dibatalkan otomatis.");
    }

    private function cancelOrder(Order $order, $tenants, $tenantIds): void
    {
        // 1. Update status order
        $order->update([
            'status'       => Order::STATUS_DIBATALKAN,
            'cancelled_at' => now(),
        ]);

        // 2. Kembalikan stok tiap item
        foreach ($order->items as $item) {
            Menu::where('id', $item->menu_id)->increment('stock', $item->quantity);
            Menu::where('id', $item->menu_id)->decrement('total_sold', $item->quantity);
        }

        $sudahBayar = $order->payment_status === 'paid';
        $telegram   = new TelegramController;

        // 3. Notif ke customer
        $chatId = $order->user->telegram_chat_id ?? null;
        if ($chatId) {
            $pesan = "❌ *Pesanan Dibatalkan Otomatis*\n\n" .
                     "Maaf, pesanan *#{$order->order_number}* dibatalkan karena kantin sudah tutup " .
                     "dan belum sempat dikonfirmasi oleh pengelola.\n\n" ;
                     

            if ($sudahBayar) {
                $pesan .= "\n\n💰 Karena pesanan ini sudah dibayar, dana kamu akan kami kembalikan.\n\n" .
                        "Mohon balas pesan ini dengan format:\n" .
                        "*Nama Bank, Nomor Rekening, Nama Pemilik Rekening*\n\n" .
                        "Contoh:\n`BCA, 1234567890, Nama Kamu`";
            }

            $telegram->sendMessage($chatId, $pesan);

            if ($sudahBayar) {
                \App\Models\RefundRequest::create([
                    'order_id' => $order->id,
                    'user_id'  => $order->user_id,
                    'status'   => 'menunggu_info',
                ]);
            }
        }

        // 4. Notif ke pengelola tiap tenant terlibat
        foreach ($tenantIds as $tenantId) {
            $tenant = $tenants->get($tenantId);
            $ownerChatId = $tenant?->user?->telegram_chat_id ?? null;

            if ($ownerChatId) {
                $telegram->sendMessage(
                    $ownerChatId,
                    "⚠️ *Pesanan Terlewat*\n\n" .
                    "Pesanan *#{$order->order_number}* otomatis dibatalkan karena tidak dikonfirmasi " .
                    "sebelum jam tutup kantin.\n\n" .
                    "Mohon lebih cepat mengonfirmasi pesanan masuk agar tidak terulang."
                );
            }
        }

        // 5. Notif ke semua admin
        $adminChatIds = User::where('role', 'admin')
            ->whereNotNull('telegram_chat_id')
            ->pluck('telegram_chat_id');

        foreach ($adminChatIds as $adminChatId) {
            $pesanAdmin = "🔔 *Auto-Cancel: Pesanan #{$order->order_number}*\n\n" .
                          "Dibatalkan otomatis karena lewat jam tutup kantin.\n" .
                          "Status pembayaran: " . ($sudahBayar ? '✅ Sudah dibayar (perlu refund manual)' : 'Belum dibayar');

            $telegram->sendMessage($adminChatId, $pesanAdmin);
        }

        Log::info("Auto-cancel: Order #{$order->order_number} dibatalkan.");
    }
}
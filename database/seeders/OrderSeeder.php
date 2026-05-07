<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $siswa   = User::where('email', 'siswa@smartcanteen.test')->first();
        $siti    = User::where('email', 'siti@smartcanteen.test')->first();
        $gudeg   = Menu::where('slug', 'nasi-gudeg-komplit')->first();
        $mie     = Menu::where('slug', 'mie-goreng-spesial')->first();
        $geprek  = Menu::where('slug', 'nasi-ayam-geprek')->first();
        $bakso   = Menu::where('slug', 'bakso-urat-jumbo')->first();
        $teh     = Menu::where('slug', 'es-teh-manis')->first();
        $pisang  = Menu::where('slug', 'pisang-goreng')->first();

        // ── Pesanan selesai (riwayat) ──────────────────────────
        $this->createOrder($siswa->id, 'SC-001', 'selesai', 'istirahat_1', [
            [$gudeg,  1],
            [$teh,    2],
        ], now()->subDays(5));

        $this->createOrder($siswa->id, 'SC-002', 'selesai', 'istirahat_2', [
            [$mie,    1],
            [$teh,    1],
        ], now()->subDays(4));

        $this->createOrder($siswa->id, 'SC-003', 'selesai', 'pulang', [
            [$geprek, 1],
        ], now()->subDays(3));

        $this->createOrder($siswa->id, 'SC-004', 'selesai', 'istirahat_1', [
            [$bakso,  1],
            [$teh,    1],
        ], now()->subDays(2));

        $this->createOrder($siswa->id, 'SC-005', 'dibatalkan', 'istirahat_1', [
            [$mie,    2],
            [$pisang, 1],
        ], now()->subDays(1));

        // ── Pesanan aktif hari ini ─────────────────────────────
        $this->createOrder($siswa->id, 'SC-006', 'diproses', 'istirahat_1', [
            [$gudeg,  1],
            [$teh,    1],
        ], now()->subMinutes(30));

        $this->createOrder($siti->id, 'SC-007', 'baru', 'istirahat_1', [
            [$mie,    1],
            [$teh,    1],
        ], now()->subMinutes(10));

        $this->createOrder($siti->id, 'SC-008', 'dikonfirmasi', 'istirahat_2', [
            [$geprek, 1],
            [$pisang, 2],
        ], now()->subMinutes(5));

        $this->command->info('✓ Orders seeded (8 orders)');
    }

    /**
     * Helper: buat 1 order beserta order_items-nya
     *
     * @param array<array{Menu, int}> $items  [[$menu, $qty], ...]
     */
    private function createOrder(
        int    $userId,
        string $orderNumber,
        string $status,
        string $pickup,
        array  $items,
        mixed  $createdAt = null
    ): void {
        $total = collect($items)->sum(fn($i) => $i[0]->price * $i[1]);

        $order = Order::create([
            'user_id'         => $userId,
            'order_number'    => $orderNumber,
            'status'          => $status,
            'pickup_schedule' => $pickup,
            'total_price'     => $total,
            'confirmed_at'    => in_array($status, ['dikonfirmasi','diproses','dikirim','selesai'])
                                    ? now() : null,
            'completed_at'    => $status === 'selesai' ? now() : null,
            'cancelled_at'    => $status === 'dibatalkan' ? now() : null,
            'created_at'      => $createdAt ?? now(),
            'updated_at'      => $createdAt ?? now(),
        ]);

        foreach ($items as [$menu, $qty]) {
            OrderItem::create([
                'order_id'   => $order->id,
                'menu_id'    => $menu->id,
                'quantity'   => $qty,
                'unit_price' => $menu->price,
                'subtotal'   => $menu->price * $qty,
            ]);
        }
    }
}

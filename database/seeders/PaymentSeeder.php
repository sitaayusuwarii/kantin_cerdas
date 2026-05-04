<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $siswa     = User::where('email', 'siswa@smartcanteen.test')->first();
        $siti      = User::where('email', 'siti@smartcanteen.test')->first();
        $pengelola = User::where('email', 'pengelola@smartcanteen.test')->first();

        // Ambil order by nomor
        $orders = Order::whereIn('order_number', ['SC-001','SC-002','SC-003','SC-004','SC-006'])
                       ->get()
                       ->keyBy('order_number');

        // SC-001 → terverifikasi
        Payment::create([
            'order_id'    => $orders['SC-001']->id,
            'user_id'     => $siswa->id,
            'amount'      => $orders['SC-001']->total_price,
            'method'      => 'transfer_bri',
            'proof_path'  => null,
            'status'      => 'terverifikasi',
            'verified_at' => now()->subDays(5),
            'verified_by' => $pengelola->id,
        ]);

        // SC-002 → terverifikasi
        Payment::create([
            'order_id'    => $orders['SC-002']->id,
            'user_id'     => $siswa->id,
            'amount'      => $orders['SC-002']->total_price,
            'method'      => 'gopay',
            'proof_path'  => null,
            'status'      => 'terverifikasi',
            'verified_at' => now()->subDays(4),
            'verified_by' => $pengelola->id,
        ]);

        // SC-003 → terverifikasi
        Payment::create([
            'order_id'    => $orders['SC-003']->id,
            'user_id'     => $siswa->id,
            'amount'      => $orders['SC-003']->total_price,
            'method'      => 'dana',
            'proof_path'  => null,
            'status'      => 'terverifikasi',
            'verified_at' => now()->subDays(3),
            'verified_by' => $pengelola->id,
        ]);

        // SC-004 → menunggu verifikasi (belum dicek)
        Payment::create([
            'order_id'   => $orders['SC-004']->id,
            'user_id'    => $siswa->id,
            'amount'     => $orders['SC-004']->total_price,
            'method'     => 'transfer_bca',
            'proof_path' => null,
            'status'     => 'menunggu',
            'note'       => 'Sudah transfer jam 09.15',
        ]);

        // SC-006 → belum bayar (tidak ada payment record)

        $this->command->info('✓ Payments seeded (4 payments)');
    }
}

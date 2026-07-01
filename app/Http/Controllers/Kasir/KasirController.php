<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class KasirController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        $paidStatuses = [
            'pembayaran_terverifikasi',
            'diproses',
            'selesai_dimasak',
            'dikirim',
            'selesai',
        ];

        $activeStatuses = [
            'pending',
            'menunggu_pembayaran',
            'menunggu_konfirmasi',
            'pembayaran_terverifikasi',
            'diproses',
            'selesai_dimasak',
            'dikirim',
        ];

        $waitingPaymentStatuses = [
            'pending',
            'menunggu_pembayaran',
            'menunggu_konfirmasi',
            'belum_bayar',
        ];

        $menus = Menu::query()
            ->with(['category', 'tenant'])
            ->when(Schema::hasColumn('menus', 'is_available'), fn ($query) => $query->where('is_available', true))
            ->latest()
            ->get();

        $categories = Category::orderBy('name')->get();

        $paymentMethods = collect([
            (object) ['code' => 'qris', 'name' => 'QRIS'],
            (object) ['code' => 'transfer_bca', 'name' => 'Transfer BCA'],
            (object) ['code' => 'cash', 'name' => 'Cash'],
        ]);

        if (Schema::hasTable('payment_methods')) {
            $paymentMethodsQuery = DB::table('payment_methods')
                ->select('code', 'name');

            if (Schema::hasColumn('payment_methods', 'is_active')) {
                $paymentMethodsQuery->where('is_active', true);
            }

            if (Schema::hasColumn('payment_methods', 'sort_order')) {
                $paymentMethodsQuery->orderBy('sort_order');
            }

            $paymentMethods = $paymentMethodsQuery->orderBy('name')->get();

            if ($paymentMethods->isEmpty()) {
                $paymentMethods = collect([(object) ['code' => 'qris', 'name' => 'QRIS']]);
            }
        }

        $totalPesananHariIni = $this->applyKasirOrderScope(Order::query())
            ->whereDate('created_at', $today)
            ->count();

        $pendapatanHariIni = $this->applyKasirOrderScope(Order::query())
            ->whereDate('created_at', $today)
            ->whereIn('status', $paidStatuses)
            ->sum('total_price');

        $pesananAktif = $this->applyKasirOrderScope(Order::query())
            ->whereDate('created_at', $today)
            ->whereIn('status', $activeStatuses)
            ->count();

        $pesananSelesai = $this->applyKasirOrderScope(Order::query())
            ->whereDate('created_at', $today)
            ->where('status', 'selesai')
            ->count();

        $perluKonfirmasiQuery = $this->applyKasirOrderScope(Order::query());

        if (Schema::hasColumn('orders', 'payment_status')) {
            $perluKonfirmasiQuery->whereIn('payment_status', $waitingPaymentStatuses);
        } else {
            $perluKonfirmasiQuery->whereIn('status', $waitingPaymentStatuses);
        }

        $jumlahPerluKonfirmasi = $perluKonfirmasiQuery->count();

        $pesananTerbaru = $this->applyKasirOrderScope(Order::with(['user', 'items.menu']))
            ->whereDate('created_at', $today)
            ->latest()
            ->limit(8)
            ->get();

        return view('kasir.dashboard', compact(
            'menus',
            'categories',
            'paymentMethods',
            'totalPesananHariIni',
            'pendapatanHariIni',
            'pesananAktif',
            'pesananSelesai',
            'jumlahPerluKonfirmasi',
            'pesananTerbaru'
        ));
    }

    public function history(Request $request)
    {
        $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $paidStatuses = [
            'pembayaran_terverifikasi',
            'diproses',
            'selesai_dimasak',
            'dikirim',
            'selesai',
        ];

        $waitingPaymentStatuses = [
            'pending',
            'menunggu_pembayaran',
            'menunggu_konfirmasi',
            'belum_bayar',
        ];

        $ordersQuery = $this->applyKasirOrderScope(Order::with(['user', 'items.menu']))
            ->latest();

        if ($request->filled('date_from')) {
            $ordersQuery->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $ordersQuery->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $ordersQuery->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $keyword = $request->search;

            $ordersQuery->where(function ($query) use ($keyword) {
                $query->where('order_number', 'like', "%{$keyword}%");

                if (Schema::hasColumn('orders', 'customer_name')) {
                    $query->orWhere('customer_name', 'like', "%{$keyword}%");
                }

                $query->orWhereHas('user', function ($userQuery) use ($keyword) {
                    $userQuery->where('name', 'like', "%{$keyword}%");

                    if (Schema::hasColumn('users', 'full_name')) {
                        $userQuery->orWhere('full_name', 'like', "%{$keyword}%");
                    }
                });
            });
        }

        $orders = $ordersQuery->paginate(10)->withQueryString();

        $perluKonfirmasiQuery = $this->applyKasirOrderScope(Order::with(['user', 'items.menu']))
            ->latest();

        if (Schema::hasColumn('orders', 'payment_status')) {
            $perluKonfirmasiQuery->whereIn('payment_status', $waitingPaymentStatuses);
        } else {
            $perluKonfirmasiQuery->whereIn('status', $waitingPaymentStatuses);
        }

        $perluKonfirmasi = $perluKonfirmasiQuery->get();

        $totalPesanan = $this->applyKasirOrderScope(Order::query())->count();
        $totalPendapatan = $this->applyKasirOrderScope(Order::query())
            ->whereIn('status', $paidStatuses)
            ->sum('total_price');
        $pesananSelesai = $this->applyKasirOrderScope(Order::query())
            ->where('status', 'selesai')
            ->count();

        return view('kasir.history', compact(
            'orders',
            'perluKonfirmasi',
            'totalPesanan',
            'totalPendapatan',
            'pesananSelesai'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => ['required', 'json'],
            'customer_name' => ['nullable', 'string', 'max:100'],
            'order_type' => ['required', 'in:dine_in,takeaway,antar_kelas'],
            'table_number' => ['nullable', 'string', 'max:50'],
            'pickup_option' => ['required', 'in:sekarang,istirahat_1,istirahat_2,pulang,atur_jam'],
            'pickup_time' => ['nullable', 'date_format:H:i'],
            'payment_method' => ['required', 'string', 'max:50'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validated['pickup_option'] === 'atur_jam' && empty($validated['pickup_time'])) {
            return back()->withInput()->withErrors([
                'pickup_time' => 'Jam pengambilan atau pengantaran wajib diisi.',
            ]);
        }

        $items = collect(json_decode($validated['items'], true))
            ->filter(fn ($item) => isset($item['id'], $item['qty']) && (int) $item['qty'] > 0)
            ->map(fn ($item) => [
                'id' => (int) $item['id'],
                'qty' => max(1, (int) $item['qty']),
            ])
            ->values();

        if ($items->isEmpty()) {
            return back()->withInput()->withErrors([
                'items' => 'Pilih minimal satu menu untuk membuat pesanan.',
            ]);
        }

        $menus = Menu::whereIn('id', $items->pluck('id'))
            ->with('tenant')
            ->get()
            ->keyBy('id');

        if ($menus->count() !== $items->count()) {
            return back()->withInput()->withErrors([
                'items' => 'Ada menu yang tidak ditemukan. Silakan pilih ulang pesanan.',
            ]);
        }

        $totalPrice = $items->sum(function ($item) use ($menus) {
            return (int) $menus[$item['id']]->price * $item['qty'];
        });

        $validated['payment_method'] = $this->normalizeOrderPaymentMethod($validated['payment_method']);

        $order = DB::transaction(function () use ($validated, $items, $menus, $totalPrice) {
            $order = new Order();
            $order->user_id = auth()->id();
            $order->order_number = $this->generateOrderNumber();
            $order->total_price = $totalPrice;
            $order->status = 'pembayaran_terverifikasi';

            if (Schema::hasColumn('orders', 'payment_status')) {
                $order->payment_status = 'paid';
            }

            if (Schema::hasColumn('orders', 'payment_method')) {
                $order->payment_method = $validated['payment_method'];
            }

            if (Schema::hasColumn('orders', 'order_type')) {
                $order->order_type = $validated['order_type'];
            }

            if (Schema::hasColumn('orders', 'table_number')) {
                $order->table_number = $validated['table_number'];
            }

            if ($validated['order_type'] === 'antar_kelas' && Schema::hasColumn('orders', 'classroom')) {
                $order->classroom = $validated['table_number'];
            }

            if (Schema::hasColumn('orders', 'pickup_schedule') && in_array($validated['pickup_option'], ['istirahat_1', 'istirahat_2', 'pulang'], true)) {
                $order->pickup_schedule = $validated['pickup_option'];
            }

            if (Schema::hasColumn('orders', 'pickup_time')) {
                $order->pickup_time = $validated['pickup_option'] === 'atur_jam'
                    ? $validated['pickup_time']
                    : $validated['pickup_option'];
            }

            if (Schema::hasColumn('orders', 'customer_name')) {
                $order->customer_name = $validated['customer_name'] ?: 'Pelanggan Kasir';
            }

            if (Schema::hasColumn('orders', 'created_by')) {
                $order->created_by = auth()->id();
            }

            if (Schema::hasColumn('orders', 'kasir_id')) {
                $order->kasir_id = auth()->id();
            }

            if (Schema::hasColumn('orders', 'order_source')) {
                $order->order_source = 'kasir';
            }

            if (Schema::hasColumn('orders', 'note')) {
                $noteParts = [];

                if (! empty($validated['customer_name'])) {
                    $noteParts[] = 'Nama pelanggan: ' . $validated['customer_name'];
                }

                if (! empty($validated['note'])) {
                    $noteParts[] = $validated['note'];
                }

                $order->note = implode(' | ', $noteParts);
            }

            $order->save();

            foreach ($items as $item) {
                $menu = $menus[$item['id']];
                $quantity = $item['qty'];
                $price = (int) $menu->price;

                $orderItem = [
                    'order_id' => $order->id,
                    'menu_id' => $menu->id,
                    'quantity' => $quantity,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (Schema::hasColumn('order_items', 'unit_price')) {
                    $orderItem['unit_price'] = $price;
                } elseif (Schema::hasColumn('order_items', 'price')) {
                    $orderItem['price'] = $price;
                }

                if (Schema::hasColumn('order_items', 'subtotal')) {
                    $orderItem['subtotal'] = $price * $quantity;
                }

                if (Schema::hasColumn('order_items', 'tenant_id')) {
                    $orderItem['tenant_id'] = $this->resolveTenantId($menu);
                }

                if (Schema::hasColumn('order_items', 'status')) {
                    $orderItem['status'] = 'menunggu';
                }

                if (Schema::hasColumn('order_items', 'tenant_status')) {
                    $orderItem['tenant_status'] = 'baru';
                }

                DB::table('order_items')->insert($orderItem);
            }

            return $order;
        });

        NotificationService::pesananKasirBaru($order->fresh(['items.menu']));

        return redirect()
            ->route('kasir.payment', ['order' => $order->order_number])
            ->with('success', 'Pesanan kasir berhasil dibuat. Silakan cek kembali detail pembayarannya.');
    }

    public function showPayment(Order $order)
    {
        $order->load(['user', 'items.menu']);

        return view('kasir.payment', compact('order'));
    }

    public function confirmPayment(Order $order)
    {
        if (Schema::hasColumn('orders', 'payment_status')) {
            $order->payment_status = 'paid';
        }

        $order->status = 'pembayaran_terverifikasi';
        $order->save();

        return redirect()
            ->route('kasir.history')
            ->with('success', 'Pembayaran berhasil dikonfirmasi oleh kasir.');
    }

    private function generateOrderNumber(): string
    {
        do {
            $number = 'KS-' . now()->format('ymd') . '-' . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }

    private function applyKasirOrderScope($query)
    {
        return $query->where(function ($scope) {
            if (Schema::hasColumn('orders', 'kasir_id')) {
                $scope->where('kasir_id', auth()->id())
                    ->orWhere('user_id', auth()->id());
            } elseif (Schema::hasColumn('orders', 'created_by')) {
                $scope->where('created_by', auth()->id())
                    ->orWhere('user_id', auth()->id());
            } else {
                $scope->where('user_id', auth()->id());
            }
        });
    }

    private function normalizeOrderPaymentMethod(string $method): string
    {
        $method = strtolower(trim($method));
        $method = str_replace([' ', '-'], '_', $method);

        return match ($method) {
            'qris', 'qr', 'qr_code' => 'qris',
            'cash', 'tunai' => 'cash',
            'bca', 'bank_bca' => 'bca',
            'bri', 'bank_bri' => 'bri',
            'transfer_bca', 'tf_bca' => 'transfer_bca',
            'transfer_bri', 'tf_bri' => 'transfer_bri',
            default => $method,
        };
    }

    private function resolveTenantId($menu): ?int
    {
        if (! empty($menu->tenant_id)) {
            return (int) $menu->tenant_id;
        }

        if (! empty($menu->seller_id) && Schema::hasTable('tenants')) {
            $tenantId = DB::table('tenants')
                ->where('user_id', $menu->seller_id)
                ->value('id');

            return $tenantId ? (int) $tenantId : null;
        }

        return null;
    }
}

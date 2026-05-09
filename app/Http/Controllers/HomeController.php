<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Menu;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $totalPesanan = Order::where('user_id', $userId)->count();

        $selesai = Order::where('user_id', $userId)
            ->where('status', 'selesai')->count();

        $proses = Order::where('user_id', $userId)
    ->whereIn('status', ['pembayaran_terverifikasi', 'dikonfirmasi', 'diproses', 'dikirim'])
    ->count();

        $tagihan = Order::where('user_id', $userId)
            ->whereIn('payment_status', ['pending', 'rejected'])
            ->whereNotIn('status', ['selesai', 'dibatalkan'])
            ->count();

        $tagihanTerbaru = Order::where('user_id', $userId)
            ->whereIn('payment_status', ['pending', 'rejected'])
            ->whereNotIn('status', ['selesai', 'dibatalkan'])
            ->latest()
            ->first();

        // Menu terpopuler
        $menuFavorit = Menu::where('is_available', true)
            ->orderByDesc('total_sold')
            ->limit(3)
            ->get();

        // Pesanan aktif
        $pesananAktif = Order::where('user_id', $userId)
            ->whereIn('status', ['baru', 'diproses'])
            ->with('items.menu')
            ->latest()
            ->first();

        return view('customer.home', compact(
            'totalPesanan', 'selesai', 'proses', 'tagihan',
            'tagihanTerbaru', 'menuFavorit', 'pesananAktif'
        ));
    }
}
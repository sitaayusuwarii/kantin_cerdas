<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        // SUMMARY
        $totalTransactions = Order::count();

        $totalRevenue = Order::sum('total_price');

        $activeMenus = Menu::where('is_available', true)->count();

        $bestMenu = Menu::orderByDesc('total_sold')->first();

        // TOP MENU
        $topMenus = OrderItem::select(
                'menu_id',
                DB::raw('SUM(quantity) as sold'),
                DB::raw('SUM(subtotal) as revenue')
            )
            ->with('menu.category')
            ->groupBy('menu_id')
            ->orderByDesc('sold')
            ->take(5)
            ->get();

        return view('pengelola.report', compact(
            'totalTransactions',
            'totalRevenue',
            'activeMenus',
            'bestMenu',
            'topMenus'
        ));
    }
}
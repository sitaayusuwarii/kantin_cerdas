<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Menu;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class LaporanFavoritController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'bulan');
        [$startDate, $endDate, $periodLabel] = $this->resolvePeriod($period);

        // Top 5 menu berdasarkan order_items
        $topMenus = OrderItem::with(['menu'])
            ->whereHas('order', fn($q) => $q
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'selesai')
            )
            ->selectRaw('menu_id, SUM(quantity) as sold, SUM(subtotal) as revenue')
            ->groupBy('menu_id')
            ->orderByDesc('sold')
            ->limit(5)
            ->get();

        // Inject rank, emoji, bar color, percentage
        $maxSold   = $topMenus->max('sold') ?: 1;
        $emojis    = ['🍛','🍜','🥗','🍱','🥘'];
        $barColors = ['bg-amber-500','bg-forest-500','bg-teal-500','bg-red-400','bg-purple-400'];

        foreach ($topMenus as $i => $m) {
            $m['r']   = $i + 1;
            $m['e']   = $emojis[$i] ?? '🍽️';
            $m['bar'] = $barColors[$i] ?? 'bg-forest-400';
            $m['pct'] = (int) round(($m->sold / $maxSold) * 100);
            $m['rev'] = $m->revenue;
        }

        // Summary cards
        $totalTransactions = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'selesai')
            ->count();

        // Total revenue dari order_items (lebih akurat)
        $totalRevenue = OrderItem::whereHas('order', fn($q) => $q
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'selesai')
        )->sum('subtotal');

        // Menu aktif = is_available true
        $activeMenus = Menu::where('is_available', true)->count();

        // Best menu
        $bestMenu = $topMenus->first()?->menu;
        if ($bestMenu) {
            $bestMenu->total_sold = $topMenus->first()?->sold;
        }

        return view('pengelola.report', compact(
            'topMenus', 'totalTransactions', 'totalRevenue',
            'activeMenus', 'bestMenu', 'period', 'periodLabel',
            'startDate', 'endDate'
        ));
    }

    public function exportExcel(Request $request)
    {
        $period = $request->get('period', 'bulan');
        [$startDate, $endDate, $periodLabel] = $this->resolvePeriod($period);

        $topMenus = OrderItem::with(['menu'])
            ->whereHas('order', fn($q) => $q
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'selesai')
            )
            ->selectRaw('menu_id, SUM(quantity) as sold, SUM(subtotal) as revenue')
            ->groupBy('menu_id')
            ->orderByDesc('sold')
            ->limit(5)
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Favorit');

        // Judul
        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', 'LAPORAN MENU FAVORIT — ' . strtoupper($periodLabel));
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2D5016']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $sheet->mergeCells('A2:F2');
        $sheet->setCellValue('A2', 'Periode: ' . $startDate->format('d M Y') . ' – ' . $endDate->format('d M Y'));
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['size' => 10, 'color' => ['rgb' => '4A5568']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Header tabel
        $headers = ['No', 'Nama Menu', 'Kategori', 'Terjual (Porsi)', 'Pendapatan (Rp)', 'Rekomendasi'];
        foreach ($headers as $col => $h) {
            $sheet->setCellValue(chr(65 + $col) . '4', $h);
        }
        $sheet->getStyle('A4:F4')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4A7C59']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
        ]);

        // Data rows
        $recMap = [1 => 'Tambah Stok +15%', 2 => 'Pertahankan Stok'];
        foreach ($topMenus as $i => $m) {
            $row = $i + 5;
            $rec = $recMap[$i + 1] ?? 'Normal';

            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $m->menu->name ?? '-');
            $sheet->setCellValue("C{$row}", $m->menu->category ?? '-'); // category di menus adalah varchar
            $sheet->setCellValue("D{$row}", $m->sold);
            $sheet->setCellValue("E{$row}", $m->revenue);
            $sheet->setCellValue("F{$row}", $rec);

            $fillColor = $i % 2 === 0 ? 'F9F7F0' : 'FFFFFF';
            $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $fillColor]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2D9C8']]],
            ]);
            $sheet->getStyle("E{$row}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("D{$row}:E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        // Lebar kolom
        foreach (['A' => 5, 'B' => 28, 'C' => 18, 'D' => 16, 'E' => 20, 'F' => 20] as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        $filename = 'laporan-favorit-' . $period . '-' . now()->format('Ymd') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        (new Xlsx($spreadsheet))->save('php://output');
        exit;
    }

    private function resolvePeriod(string $period): array
    {
        return match ($period) {
            'hari'   => [
                Carbon::today(),
                Carbon::today()->endOfDay(),
                'Hari Ini — ' . Carbon::today()->translatedFormat('d F Y')
            ],
            'minggu' => [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
                'Minggu Ini — ' . Carbon::now()->startOfWeek()->format('d M') . ' s/d ' . Carbon::now()->endOfWeek()->format('d M Y')
            ],
            default  => [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
                'Bulan Ini — ' . Carbon::now()->translatedFormat('F Y')
            ],
        };
    }
}
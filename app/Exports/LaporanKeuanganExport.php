<?php

namespace App\Exports;

use App\Models\Payment;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LaporanKeuanganExport implements FromCollection, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;
    protected $monthlyData;
    protected $totalIncome;
    protected $totalTransactions;

    public function __construct($startDate, $endDate, $monthlyData, $totalIncome, $totalTransactions)
    {
        $this->startDate         = $startDate;
        $this->endDate           = $endDate;
        $this->monthlyData       = $monthlyData;
        $this->totalIncome       = $totalIncome;
        $this->totalTransactions = $totalTransactions;
    }

    public function collection()
    {
        $rows = collect();

        foreach ($this->monthlyData as $row) {
            $rows->push([
                'Bulan'            => $row['month'],
                'Total Transaksi'  => $row['trx'],
                'Pemasukan (Rp)'   => $row['income'],
                'Growth'           => $row['growth'] ?? '-',
            ]);
        }

        // Baris total
        $rows->push([
            'Bulan'            => 'TOTAL YTD',
            'Total Transaksi'  => $this->totalTransactions,
            'Pemasukan (Rp)'   => $this->totalIncome,
            'Growth'           => '-',
        ]);

        return $rows;
    }

    public function headings(): array
    {
        return ['Bulan', 'Total Transaksi', 'Pemasukan (Rp)', 'Growth'];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = count($this->monthlyData) + 2; // +1 header +1 total

        return [
            // Header row
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4f46e5']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            // Baris total
            $lastRow => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'e0e7ff']],
            ],
        ];
    }

    public function title(): string
    {
        return 'Laporan Keuangan';
    }
}
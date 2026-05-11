<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #1e293b; }
        h1   { font-size: 18px; margin-bottom: 4px; }
        p    { margin: 0 0 2px; color: #64748b; }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th    { background: #ea580c; color: white; padding: 8px 12px; text-align: left; font-size: 11px; }
        td    { padding: 7px 12px; border-bottom: 1px solid #e2e8f0; font-size: 11px; }
        tr:nth-child(even) td { background: #f8fafc; }
        tfoot td { background: #ffedd5; font-weight: bold; }

        .header { margin-bottom: 20px; }
        .stats  { display: flex; gap: 16px; margin: 16px 0; }
        .stat-box { border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 16px; flex: 1; }
        .stat-box .label { font-size: 10px; color: #94a3b8; }
        .stat-box .value { font-size: 16px; font-weight: bold; margin-top: 4px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Keuangan SmartCanteen</h1>
        <p>Periode: {{ $startDate->format('d M Y') }} – {{ $endDate->format('d M Y') }}</p>
        <p>Dicetak: {{ now()->locale('id')->isoFormat('dddd, D MMMM Y HH:mm') }} WIB</p>
    </div>

    <div class="stats">
        <div class="stat-box">
            <div class="label">Total Pemasukan</div>
            <div class="value">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Total Transaksi</div>
            <div class="value">{{ number_format($totalTransactions) }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Rata-rata / Transaksi</div>
            <div class="value">Rp {{ number_format($avgPerTransaction, 0, ',', '.') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Bulan</th>
                <th>Total Transaksi</th>
                <th>Pemasukan (Rp)</th>
                <th>Growth</th>
            </tr>
        </thead>
        <tbody>
            @foreach($monthlyData as $row)
            <tr>
                <td>{{ $row['month'] }}</td>
                <td>{{ number_format($row['trx']) }}</td>
                <td>Rp {{ number_format($row['income'], 0, ',', '.') }}</td>
                <td>{{ $row['growth'] ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td>TOTAL YTD</td>
                <td>{{ number_format($totalTransactions) }}</td>
                <td>Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
                <td>—</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
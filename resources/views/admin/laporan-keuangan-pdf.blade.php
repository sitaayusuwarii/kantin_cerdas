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
        <table style="width:100%; margin:0; border:none;">
            <tr>
                <td style="border:none; padding:0; vertical-align:middle; width:72px;">
                    <img src="{{ public_path('images/canteen.png') }}"
                        style="width:64px; height:64px; object-fit:contain;">
                </td>
                <td style="border:none; padding:0 0 0 14px; vertical-align:middle;">
                    <h1 style="margin:0 0 3px; font-size:20px; color:#ea580c;">SmartCanteen</h1>
                    <p style="margin:0 0 2px; font-size:12px; font-weight:600; color:#1e293b;">Laporan Keuangan</p>
                    <p style="margin:0; font-size:10px; color:#64748b;">
                        Periode: {{ $startDate->format('d M Y') }} – {{ $endDate->format('d M Y') }}
                    </p>
                    <p style="margin:0; font-size:10px; color:#64748b;">
                        Dicetak: {{ now()->locale('id')->isoFormat('dddd, D MMMM Y HH:mm') }} WIB
                    </p>
                </td>
                <td style="border:none; padding:0; vertical-align:middle; text-align:right;">
                    <div style="display:inline-block; background:#fff7ed; border:1px solid #fed7aa;
                                border-radius:8px; padding:8px 14px; text-align:center;">
                        <div style="font-size:9px; color:#ea580c; font-weight:600; letter-spacing:1px; text-transform:uppercase;">
                            Dokumen Resmi
                        </div>
                        <div style="font-size:10px; color:#64748b; margin-top:2px;">
                            {{ now()->format('Y') }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        <hr style="border:none; border-top:2px solid #ea580c; margin:14px 0 0;">
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
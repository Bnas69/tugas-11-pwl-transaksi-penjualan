<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; }
        table { border-collapse: collapse; width: 100%; }
        th { background: #1f4e79; color: #ffffff; font-weight: bold; }
        th, td { border: 1px solid #999999; padding: 8px; font-size: 12px; }
        .text { mso-number-format:"\@"; }
        .number { mso-number-format:"0"; }
    </style>
</head>
<body>
    <h2>Laporan Transaksi Penjualan</h2>
    <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Invoice</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th>Metode Pembayaran</th>
                <th>Jumlah Item</th>
                <th>Subtotal</th>
                <th>Diskon</th>
                <th>Pajak</th>
                <th>Total</th>
                <th>Bayar</th>
                <th>Kembalian</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($sales as $sale)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="text">{{ $sale->invoice_number }}</td>
                    <td>{{ $sale->sale_date->format('d/m/Y') }}</td>
                    <td>{{ $sale->customer->name ?? 'Pelanggan umum' }}</td>
                    <td>{{ $sale->payment_method }}</td>
                    <td class="number">{{ $sale->items->sum('quantity') }}</td>
                    <td class="number">{{ (int) $sale->subtotal }}</td>
                    <td class="number">{{ (int) $sale->discount }}</td>
                    <td class="number">{{ (int) $sale->tax }}</td>
                    <td class="number">{{ (int) $sale->grand_total }}</td>
                    <td class="number">{{ (int) $sale->paid_amount }}</td>
                    <td class="number">{{ (int) $sale->change_amount }}</td>
                    <td>{{ $sale->status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="13">Belum ada transaksi penjualan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

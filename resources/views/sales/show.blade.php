@extends('layouts.app')

@section('title', $sale->invoice_number)
@section('page-title', 'Detail Invoice')
@section('eyebrow', $sale->invoice_number)

@section('content')
    <section class="invoice-card">
        <div class="invoice-head">
            <div>
                <span class="eyebrow">Invoice Penjualan</span>
                <h2>{{ $sale->invoice_number }}</h2>
                <p>{{ $sale->sale_date->format('d M Y') }} - {{ $sale->payment_method }}</p>
            </div>
            <button class="secondary-button no-print" type="button" onclick="window.print()">Cetak</button>
        </div>

        <div class="invoice-meta">
            <div>
                <span>Pelanggan</span>
                <strong>{{ $sale->customer->name ?? 'Pelanggan umum' }}</strong>
                <small>{{ $sale->customer->phone ?? 'Tanpa nomor telepon' }}</small>
            </div>
            <div>
                <span>Admin</span>
                <strong>{{ $sale->user->name ?? 'Admin' }}</strong>
                <small>{{ $sale->status }}</small>
            </div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sale->items as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->product_name }}</strong>
                                <small>{{ $item->product_sku }}</small>
                            </td>
                            <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>Rp {{ number_format($item->line_total, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="invoice-total">
            <div><span>Subtotal</span><strong>Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</strong></div>
            <div><span>Diskon</span><strong>Rp {{ number_format($sale->discount, 0, ',', '.') }}</strong></div>
            <div><span>Pajak / Biaya</span><strong>Rp {{ number_format($sale->tax, 0, ',', '.') }}</strong></div>
            <div class="grand"><span>Total</span><strong>Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</strong></div>
            <div><span>Dibayar</span><strong>Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</strong></div>
            <div><span>Kembalian</span><strong>Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</strong></div>
        </div>

        @if ($sale->notes)
            <p class="invoice-note">{{ $sale->notes }}</p>
        @endif
    </section>
@endsection

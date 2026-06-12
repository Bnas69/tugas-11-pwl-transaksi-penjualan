@extends('layouts.app')

@section('title', 'Penjualan')
@section('page-title', 'Modul Penjualan')
@section('eyebrow', 'Transaksi')

@section('content')
    <section class="page-intro">
        <div>
            <span class="eyebrow">Laporan transaksi</span>
            <h2>Riwayat invoice penjualan</h2>
        </div>
        <p>Filter, export, dan cek detail transaksi dari satu halaman dengan tampilan yang lebih ringkas.</p>
    </section>

    <section class="toolbar-card">
        <form class="search-form" method="GET" action="{{ route('penjualan.index') }}">
            <input type="search" name="cari" value="{{ $search }}" placeholder="Cari nomor invoice atau nama pelanggan">
            <button class="secondary-button" type="submit">Cari</button>
        </form>

        <div class="toolbar-actions">
            <a class="export-button" href="{{ route('penjualan.export.excel', request()->only('cari')) }}">Export Excel</a>
            <a class="secondary-button" href="{{ route('penjualan.export.csv', request()->only('cari')) }}">Export CSV</a>
            <a class="secondary-button" href="{{ route('penjualan.export.sql', request()->only('cari')) }}">Export SQL</a>
            <a class="primary-button" href="{{ route('penjualan.create') }}">Transaksi Baru</a>
        </div>
    </section>

    <section class="table-card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Pembayaran</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sales as $sale)
                        <tr>
                            <td data-label="Invoice">
                                <strong>{{ $sale->invoice_number }}</strong>
                                <small>{{ $sale->status }}</small>
                            </td>
                            <td data-label="Tanggal">{{ $sale->sale_date->format('d M Y') }}</td>
                            <td data-label="Pelanggan">{{ $sale->customer->name ?? 'Pelanggan umum' }}</td>
                            <td data-label="Pembayaran"><span class="badge badge-blue">{{ $sale->payment_method }}</span></td>
                            <td data-label="Total">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
                            <td data-label="Aksi">
                                <div class="row-actions">
                                    <a href="{{ route('penjualan.show', $sale) }}">Detail</a>
                                    <form method="POST" action="{{ route('penjualan.destroy', $sale) }}" onsubmit="return confirm('Batalkan transaksi dan kembalikan stok?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit">Batalkan</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-cell">Belum ada transaksi penjualan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $sales->links() }}
    </section>
@endsection

@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Penjualan')
@section('eyebrow', 'Kelvin Maulana - 411232020')

@section('content')
    @php
        $chartMax = max((int) $bestProducts->max('sold_qty'), 1);
    @endphp

    <section class="dashboard-hero">
        <div class="hero-copy">
            <span class="eyebrow light">Modern academic dashboard</span>
            <h2>Ringkasan operasional penjualan dalam satu layar.</h2>
            <p>Data produk, pelanggan, transaksi, omzet, stok rendah, dan produk terlaris disusun agar mudah dipantau tanpa mengubah proses aplikasi.</p>
        </div>
        <div class="hero-panel">
            <span>Identitas</span>
            <strong>Kelvin Maulana</strong>
            <small>411232020 - Teknik Informatika</small>
            <a class="primary-button full" href="{{ route('penjualan.create') }}">Buat Transaksi</a>
        </div>
    </section>

    <section class="stats-grid">
        <article class="stat-card">
            <span class="stat-icon">P</span>
            <div>
                <span>Produk</span>
                <strong>{{ $productCount }}</strong>
                <small>Total data barang</small>
            </div>
        </article>
        <article class="stat-card">
            <span class="stat-icon soft">C</span>
            <div>
                <span>Pelanggan</span>
                <strong>{{ $customerCount }}</strong>
                <small>Kontak tersimpan</small>
            </div>
        </article>
        <article class="stat-card">
            <span class="stat-icon calm">T</span>
            <div>
                <span>Transaksi</span>
                <strong>{{ $transactionCount }}</strong>
                <small>Invoice dibuat</small>
            </div>
        </article>
        <article class="stat-card revenue">
            <span class="stat-icon dark">R</span>
            <div>
                <span>Omzet Bulan Ini</span>
                <strong>Rp {{ number_format($monthRevenue, 0, ',', '.') }}</strong>
                <small>Hari ini Rp {{ number_format($todayRevenue, 0, ',', '.') }}</small>
            </div>
        </article>
    </section>

    <section class="dashboard-grid">
        <article class="panel-card">
            <div class="section-heading">
                <div>
                    <span class="eyebrow">Aktivitas</span>
                    <h3>Transaksi Terbaru</h3>
                </div>
                <a href="{{ route('penjualan.index') }}">Lihat semua</a>
            </div>

            <div class="mini-list">
                @forelse ($latestSales as $sale)
                    <a href="{{ route('penjualan.show', $sale) }}" class="mini-row">
                        <span class="mini-dot"></span>
                        <span>
                            <strong>{{ $sale->invoice_number }}</strong>
                            <small>{{ $sale->customer->name ?? 'Pelanggan umum' }} - {{ $sale->sale_date->format('d M Y') }}</small>
                        </span>
                        <b>Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</b>
                    </a>
                @empty
                    <p class="empty-state">Belum ada transaksi. Mulai dari tombol "Buat Transaksi".</p>
                @endforelse
            </div>
        </article>

        <article class="panel-card">
            <div class="section-heading">
                <div>
                    <span class="eyebrow">Kontrol Stok</span>
                    <h3>Produk Hampir Habis</h3>
                </div>
                <a href="{{ route('produk.index') }}">Kelola produk</a>
            </div>

            <div class="stock-stack">
                @forelse ($lowStockProducts as $product)
                    <div class="stock-line">
                        <span>
                            <strong>{{ $product->name }}</strong>
                            <small>Batas pantau stok rendah</small>
                        </span>
                        <b>{{ $product->stock }} {{ $product->unit }}</b>
                    </div>
                @empty
                    <p class="empty-state">Stok aman. Tidak ada produk di bawah batas 5.</p>
                @endforelse
            </div>
        </article>

        <article class="panel-card wide chart-panel">
            <div class="section-heading">
                <div>
                    <span class="eyebrow">Insight</span>
                    <h3>Grafik Produk Terlaris</h3>
                </div>
            </div>

            <div class="chart-list">
                @forelse ($bestProducts as $product)
                    <div class="chart-row" style="--bar: {{ min(100, round(($product->sold_qty / $chartMax) * 100)) }}%;">
                        <span class="chart-rank">{{ $loop->iteration }}</span>
                        <div class="chart-info">
                            <strong>{{ $product->product_name }}</strong>
                            <small>{{ $product->sold_qty }} terjual - Rp {{ number_format($product->total_sales, 0, ',', '.') }}</small>
                        </div>
                        <div class="chart-track"><span></span></div>
                    </div>
                @empty
                    <p class="empty-state">Data produk terlaris akan muncul setelah ada transaksi.</p>
                @endforelse
            </div>
        </article>
    </section>
@endsection

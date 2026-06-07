@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Admin')
@section('eyebrow', 'Ringkasan Modul Penjualan')

@section('content')
    <section class="hero-strip">
        <div>
            <span class="eyebrow">Septian Dwi Saputra - 411232056</span>
            <h2>Ringkasan Sistem Penjualan</h2>
            <p>Dashboard ini menampilkan data produk, pelanggan, transaksi, omzet, stok hampir habis, dan produk terlaris dalam satu halaman.</p>
        </div>
        <a class="primary-button" href="{{ route('penjualan.create') }}">Buat Transaksi</a>
    </section>

    <section class="stats-grid">
        <article class="stat-card accent-blue">
            <span>Produk</span>
            <strong>{{ $productCount }}</strong>
            <small>Total data barang</small>
        </article>
        <article class="stat-card accent-green">
            <span>Pelanggan</span>
            <strong>{{ $customerCount }}</strong>
            <small>Kontak tersimpan</small>
        </article>
        <article class="stat-card accent-orange">
            <span>Transaksi</span>
            <strong>{{ $transactionCount }}</strong>
            <small>Invoice dibuat</small>
        </article>
        <article class="stat-card accent-dark">
            <span>Omzet Bulan Ini</span>
            <strong>Rp {{ number_format($monthRevenue, 0, ',', '.') }}</strong>
            <small>Hari ini Rp {{ number_format($todayRevenue, 0, ',', '.') }}</small>
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
                        <span>{{ $product->name }}</span>
                        <strong>{{ $product->stock }} {{ $product->unit }}</strong>
                    </div>
                @empty
                    <p class="empty-state">Stok aman. Tidak ada produk di bawah batas 5.</p>
                @endforelse
            </div>
        </article>

        <article class="panel-card wide">
            <div class="section-heading">
                <div>
                    <span class="eyebrow">Insight</span>
                    <h3>Produk Terlaris</h3>
                </div>
            </div>

            <div class="best-grid">
                @forelse ($bestProducts as $product)
                    <div class="best-card">
                        <span>{{ $loop->iteration }}</span>
                        <strong>{{ $product->product_name }}</strong>
                        <small>{{ $product->sold_qty }} terjual - Rp {{ number_format($product->total_sales, 0, ',', '.') }}</small>
                    </div>
                @empty
                    <p class="empty-state">Data produk terlaris akan muncul setelah ada transaksi.</p>
                @endforelse
            </div>
        </article>
    </section>
@endsection

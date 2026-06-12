@extends('layouts.app')

@section('title', 'Produk')
@section('page-title', 'Modul Produk')
@section('eyebrow', 'Master Data')

@section('content')
    <section class="page-intro">
        <div>
            <span class="eyebrow">Kelola katalog</span>
            <h2>Data produk siap jual</h2>
        </div>
        <p>Gunakan halaman ini untuk memantau SKU, kategori, harga, stok, dan status produk tanpa mengubah alur data utama.</p>
    </section>

    <section class="toolbar-card">
        <form class="search-form" method="GET" action="{{ route('produk.index') }}">
            <input type="search" name="cari" value="{{ $search }}" placeholder="Cari SKU, nama, atau kategori produk">
            <button class="secondary-button" type="submit">Cari</button>
        </form>
        <a class="primary-button" href="{{ route('produk.create') }}">Tambah Produk</a>
    </section>

    <section class="table-card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Kategori</th>
                        <th>Harga Jual</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td data-label="Produk">
                                <strong>{{ $product->name }}</strong>
                                <small>{{ $product->sku }}</small>
                            </td>
                            <td data-label="Kategori">{{ $product->category ?? '-' }}</td>
                            <td data-label="Harga Jual">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                            <td data-label="Stok">{{ $product->stock }} {{ $product->unit }}</td>
                            <td data-label="Status">
                                <span class="badge {{ $product->is_active ? 'badge-green' : 'badge-muted' }}">
                                    {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td data-label="Aksi">
                                <div class="row-actions">
                                    <a href="{{ route('produk.edit', $product) }}">Edit</a>
                                    <form method="POST" action="{{ route('produk.destroy', $product) }}" onsubmit="return confirm('Hapus atau nonaktifkan produk ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-cell">Belum ada produk yang tersimpan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $products->links() }}
    </section>
@endsection

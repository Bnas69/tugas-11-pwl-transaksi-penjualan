@extends('layouts.app')

@section('title', 'Pelanggan')
@section('page-title', 'Modul Pelanggan')
@section('eyebrow', 'Master Data')

@section('content')
    <section class="toolbar-card">
        <form class="search-form" method="GET" action="{{ route('pelanggan.index') }}">
            <input type="search" name="cari" value="{{ $search }}" placeholder="Cari nama, telepon, email, atau tipe pelanggan">
            <button class="secondary-button" type="submit">Cari</button>
        </form>
        <a class="primary-button" href="{{ route('pelanggan.create') }}">Tambah Pelanggan</a>
    </section>

    <section class="table-card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Kontak</th>
                        <th>Tipe</th>
                        <th>Transaksi</th>
                        <th>Alamat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customers as $customer)
                        <tr>
                            <td><strong>{{ $customer->name }}</strong></td>
                            <td>
                                <span>{{ $customer->phone ?? '-' }}</span>
                                <small>{{ $customer->email ?? 'email belum diisi' }}</small>
                            </td>
                            <td><span class="badge badge-blue">{{ $customer->type }}</span></td>
                            <td>{{ $customer->sales_count }} invoice</td>
                            <td>{{ $customer->address ? str($customer->address)->limit(42) : '-' }}</td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('pelanggan.edit', $customer) }}">Edit</a>
                                    <form method="POST" action="{{ route('pelanggan.destroy', $customer) }}" onsubmit="return confirm('Hapus pelanggan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-cell">Belum ada pelanggan yang tersimpan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $customers->links() }}
    </section>
@endsection

@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
    <main class="landing-shell">
        <section class="landing-panel">
            <div class="landing-copy">
                <span class="eyebrow">Pemrograman Web Lanjut</span>

                <h1>Kelvin Sales Admin</h1>
                <p>Sistem manajemen penjualan sederhana untuk mengelola produk, pelanggan, transaksi, invoice, dan laporan export dalam satu dashboard akademik yang rapi.</p>

                <div class="student-info">
                    <span>Kelvin Maulana</span>
                    <span>411232020</span>
                    <span>Pertemuan 11</span>
                </div>

                <div class="landing-actions">
                    @auth
                        <a class="primary-button" href="{{ route('dashboard') }}">Masuk Dashboard</a>
                    @else
                        <a class="primary-button" href="{{ route('login') }}">Login Admin</a>
                    @endauth

                    <a class="secondary-button" href="https://www.undira.ac.id" target="_blank" rel="noreferrer">
                        Universitas Dian Nusantara
                    </a>
                </div>
            </div>

            <div class="landing-visual" aria-label="Profil aplikasi">
                <div class="campus-photo">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo Universitas Dian Nusantara">
                </div>
                <div class="module-summary">
                    <span>Modul aktif</span>
                    <strong>Produk, Pelanggan, Penjualan</strong>
                    <small>Dashboard, invoice, export Excel, CSV, dan SQL.</small>
                </div>
            </div>
        </section>
    </main>
@endsection

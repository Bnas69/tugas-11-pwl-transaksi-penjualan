@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
    <main class="landing-shell">
        <section class="landing-card">
            <div class="landing-copy">
                <span class="eyebrow">Pemrograman Web Lanjut</span>

                <h1>Manajemen Transaksi Penjualan</h1>

                <div class="student-info">
                    <span>Septian Dwi Saputra</span>
                    <span>411232056</span>
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

            <div class="module-orbit" aria-label="Profil aplikasi">
                <div class="orbit-center">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo SalesLab">
                    <span>SalesLab</span>
                </div>
            </div>
        </section>
    </main>
@endsection

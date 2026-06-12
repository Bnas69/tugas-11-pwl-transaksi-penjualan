@extends('layouts.app')

@section('title', 'Login Admin')

@section('content')
    <main class="login-shell">
        <section class="login-panel">
            <div class="login-visual">
                <div class="login-brand">
                    <span class="brand-mark large">KM</span>
                    <span>
                        <strong>Kelvin Sales Admin</strong>
                        <small>Modern academic dashboard</small>
                    </span>
                </div>

                <div class="login-copy">
                    <span class="eyebrow light">Pemrograman Web Lanjut</span>
                    <h1>Kelola transaksi penjualan dengan tampilan yang lebih rapi.</h1>
                    <p>Panel ini disusun untuk tugas mahasiswa Teknik Informatika: ringan, responsif, dan mudah dibaca saat digunakan maupun didokumentasikan.</p>
                </div>

                <div class="login-identity">
                    <span>Nama</span>
                    <strong>Kelvin Maulana</strong>
                    <span>NIM</span>
                    <strong>411232020</strong>
                </div>
            </div>

            <div class="login-form-wrap">
                <form class="form-card login-form" method="POST" action="{{ route('login.store') }}">
                    @csrf

                    <span class="eyebrow">Masuk sistem</span>
                    <h2>Login Admin</h2>
                    <p class="helper-text">Gunakan akun admin yang sudah tersedia pada database aplikasi.</p>

                    @if ($errors->any())
                        <div class="notice notice-error compact">{{ $errors->first() }}</div>
                    @endif

                    <label>
                        Email
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="admin@test.com" required autofocus>
                    </label>

                    <label>
                        Password
                        <input type="password" name="password" placeholder="Masukkan password" required>
                    </label>

                    <label class="check-line">
                        <input type="checkbox" name="remember" value="1">
                        Ingat sesi login
                    </label>

                    <button class="primary-button full" type="submit">Masuk Dashboard</button>
                </form>
            </div>
        </section>
    </main>
@endsection

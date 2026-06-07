@extends('layouts.app')

@section('title', 'Login Admin')

@section('content')
    <main class="login-shell">
        <section class="login-card">
            <div class="login-art">
                <span class="brand-mark large">SL</span>
                <h1>Admin Penjualan</h1>
            </div>

            <form class="form-card login-form" method="POST" action="{{ route('login.store') }}">
                @csrf

                <span class="eyebrow">Masuk Sistem</span>
                <h2>SalesLab PWL</h2>

                @if ($errors->any())
                    <div class="notice notice-error compact">{{ $errors->first() }}</div>
                @endif

                <label>
                    Email
                    <input type="email" name="email" value="{{ old('email', 'admin@septian.test') }}" required autofocus>
                </label>

                <label>
                    Password
                    <input type="password" name="password" placeholder="Masukkan password" required>
                </label>

                <label class="check-line">
                    <input type="checkbox" name="remember" value="1">
                    Ingat saya
                </label>

                <button class="primary-button full" type="submit">Login Admin</button>
            </form>
        </section>
    </main>
@endsection

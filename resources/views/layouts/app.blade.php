<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Dashboard') - {{ config('app.name', 'Kelvin Sales Admin') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800|plus-jakarta-sans:400,500,600,700" rel="stylesheet" />
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <script defer src="{{ asset('js/app.js') }}"></script>
    </head>
    <body>
        @auth
            @php
                $navItems = [
                    ['label' => 'Dashboard', 'route' => 'dashboard', 'pattern' => 'dashboard', 'icon' => 'D', 'hint' => 'Ringkasan'],
                    ['label' => 'Produk', 'route' => 'produk.index', 'pattern' => 'produk.*', 'icon' => 'P', 'hint' => 'Master barang'],
                    ['label' => 'Pelanggan', 'route' => 'pelanggan.index', 'pattern' => 'pelanggan.*', 'icon' => 'C', 'hint' => 'Relasi customer'],
                    ['label' => 'Penjualan', 'route' => 'penjualan.index', 'pattern' => 'penjualan.*', 'icon' => 'T', 'hint' => 'Transaksi'],
                ];
            @endphp

            <div class="app-frame">
                <div class="sidebar-scrim" data-sidebar-close></div>

                <aside class="sidebar" id="sidebar">
                    <div class="sidebar-head">
                        <a href="{{ route('dashboard') }}" class="brand-lockup">
                            <span class="brand-mark">KM</span>
                            <span>
                                <strong>Kelvin Sales</strong>
                                <small>Academic Admin Panel</small>
                            </span>
                        </a>

                        <button class="sidebar-close" type="button" data-sidebar-close aria-label="Tutup menu">X</button>
                    </div>

                    <nav class="nav-stack">
                        <span class="nav-caption">Menu utama</span>
                        @foreach ($navItems as $item)
                            <a href="{{ route($item['route']) }}" class="nav-link {{ request()->routeIs($item['pattern']) ? 'active' : '' }}">
                                <span>{{ $item['icon'] }}</span>
                                <b>{{ $item['label'] }}</b>
                                <small>{{ $item['hint'] }}</small>
                            </a>
                        @endforeach
                    </nav>

                    <div class="student-card">
                        <span>Identitas mahasiswa</span>
                        <strong>Kelvin Maulana</strong>
                        <small>411232020 - Teknik Informatika</small>
                    </div>
                </aside>

                <main class="main-panel">
                    <header class="topbar">
                        <button class="menu-button" type="button" data-sidebar-toggle aria-label="Buka menu">
                            <span></span>
                            <span></span>
                            <span></span>
                        </button>

                        <div class="page-title-block">
                            <p class="eyebrow">@yield('eyebrow', 'Universitas Dian Nusantara')</p>
                            <h1>@yield('page-title', 'Dashboard Admin')</h1>
                        </div>

                        <div class="topbar-actions">
                            <span class="admin-pill">
                                <small>Mahasiswa</small>
                                Kelvin Maulana
                            </span>
                            <span class="student-pill">Kelvin Maulana - 411232020</span>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="ghost-button" type="submit">Keluar</button>
                            </form>
                        </div>
                    </header>

                    @if (session('success'))
                        <div class="notice notice-success">{{ session('success') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="notice notice-error">
                            <strong>Perlu diperbaiki:</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="content-stack">
                        @yield('content')
                    </div>

                    <footer class="app-footer">
                        <span>Kelvin Maulana - 411232020</span>
                        <span>Pemrograman Web Lanjut</span>
                    </footer>
                </main>
            </div>
        @else
            @if (session('success'))
                <div class="floating-notice notice notice-success">{{ session('success') }}</div>
            @endif

            @yield('content')
        @endauth
    </body>
</html>

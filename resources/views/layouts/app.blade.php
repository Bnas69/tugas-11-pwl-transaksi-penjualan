<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Dashboard') - {{ config('app.name', 'SalesLab PWL') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800|plus-jakarta-sans:400,500,600,700" rel="stylesheet" />
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <script defer src="{{ asset('js/app.js') }}"></script>
    </head>
    <body>
        @auth
            @php
                $navItems = [
                    ['label' => 'Dashboard', 'route' => 'dashboard', 'pattern' => 'dashboard', 'icon' => 'DS'],
                    ['label' => 'Produk', 'route' => 'produk.index', 'pattern' => 'produk.*', 'icon' => 'PR'],
                    ['label' => 'Pelanggan', 'route' => 'pelanggan.index', 'pattern' => 'pelanggan.*', 'icon' => 'PL'],
                    ['label' => 'Penjualan', 'route' => 'penjualan.index', 'pattern' => 'penjualan.*', 'icon' => 'PJ'],
                ];
            @endphp

            <div class="app-frame">
                <aside class="sidebar" id="sidebar">
                    <a href="{{ route('dashboard') }}" class="brand-lockup">
                        <span class="brand-mark">SL</span>
                        <span>
                            <strong>SalesLab</strong>
                            <small>Admin Penjualan</small>
                        </span>
                    </a>

                    <nav class="nav-stack">
                        @foreach ($navItems as $item)
                            <a href="{{ route($item['route']) }}" class="nav-link {{ request()->routeIs($item['pattern']) ? 'active' : '' }}">
                                <span>{{ $item['icon'] }}</span>
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </nav>

                    <div class="student-card">
                        <span>Tugas PWL</span>
                        <strong>Septian Dwi Saputra</strong>
                        <small>411232056 - Teknik Informatika</small>
                    </div>
                </aside>

                <main class="main-panel">
                    <header class="topbar">
                        <button class="menu-button" type="button" data-sidebar-toggle>Menu</button>
                        <div>
                            <p class="eyebrow">@yield('eyebrow', 'Universitas Dian Nusantara')</p>
                            <h1>@yield('page-title', 'Dashboard Admin')</h1>
                        </div>
                        <div class="topbar-actions">
                            <span class="admin-pill">{{ auth()->user()->name }}</span>
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

                    @yield('content')
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

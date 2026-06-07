<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::resource('/produk', ProductController::class)->parameters([
        'produk' => 'product',
    ])->except(['show']);

    Route::resource('/pelanggan', CustomerController::class)->parameters([
        'pelanggan' => 'customer',
    ])->except(['show']);

    Route::get('/penjualan/export/excel', [SaleController::class, 'exportExcel'])->name('penjualan.export.excel');
    Route::get('/penjualan/export/csv', [SaleController::class, 'exportCsv'])->name('penjualan.export.csv');
    Route::get('/penjualan/export/sql', [SaleController::class, 'exportSql'])->name('penjualan.export.sql');

    Route::resource('/penjualan', SaleController::class)
        ->parameters(['penjualan' => 'sale'])
        ->except(['edit', 'update']);
});

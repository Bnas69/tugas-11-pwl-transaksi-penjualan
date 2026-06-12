@extends('layouts.app')

@section('title', 'Transaksi Baru')
@section('page-title', 'Transaksi Baru')
@section('eyebrow', 'Modul Penjualan')

@section('content')
    @php
        $oldProductIds = old('product_ids', ['']);
        $oldQuantities = old('quantities', [1]);
    @endphp

    <section class="page-intro">
        <div>
            <span class="eyebrow">Input transaksi</span>
            <h2>Catat penjualan baru</h2>
        </div>
        <p>Pilih pelanggan, susun item pembelian, lalu sistem menghitung subtotal, total akhir, dan kembalian secara otomatis.</p>
    </section>

    <form class="sales-form" method="POST" action="{{ route('penjualan.store') }}" data-sale-form>
        @csrf

        <section class="form-card">
            <div class="section-heading">
                <div>
                    <span class="eyebrow">Informasi Invoice</span>
                    <h3>Data transaksi</h3>
                </div>
            </div>

            <div class="form-grid">
                <label>
                    Tanggal
                    <input type="date" name="sale_date" value="{{ old('sale_date', now()->toDateString()) }}" required>
                </label>

                <label>
                    Pelanggan
                    <select name="customer_id">
                        <option value="">Pelanggan umum</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" @selected((string) old('customer_id') === (string) $customer->id)>
                                {{ $customer->name }} - {{ $customer->type }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label>
                    Metode Bayar
                    <select name="payment_method" required>
                        @foreach (['Tunai', 'Transfer', 'QRIS', 'Debit'] as $method)
                            <option value="{{ $method }}" @selected(old('payment_method', 'Tunai') === $method)>{{ $method }}</option>
                        @endforeach
                    </select>
                </label>

                <label>
                    Catatan
                    <input type="text" name="notes" value="{{ old('notes') }}" placeholder="Opsional">
                </label>
            </div>
        </section>

        <section class="form-card">
            <div class="section-heading">
                <div>
                    <span class="eyebrow">Keranjang</span>
                    <h3>Produk yang dibeli</h3>
                </div>
                <button class="secondary-button" type="button" data-add-sale-row>Tambah Baris</button>
            </div>

            <div class="sale-items" data-sale-items>
                @foreach ($oldProductIds as $index => $oldProductId)
                    <div class="sale-item-row" data-sale-row>
                        <label>
                            Produk
                            <select name="product_ids[]" data-product-select required>
                                <option value="" data-price="0" data-stock="0">Pilih produk</option>
                                @foreach ($products as $product)
                                    <option
                                        value="{{ $product->id }}"
                                        data-price="{{ $product->selling_price }}"
                                        data-stock="{{ $product->stock }}"
                                        @selected((string) $oldProductId === (string) $product->id)
                                    >
                                        {{ $product->name }} - Rp {{ number_format($product->selling_price, 0, ',', '.') }} (stok {{ $product->stock }})
                                    </option>
                                @endforeach
                            </select>
                        </label>

                        <label>
                            Qty
                            <input type="number" name="quantities[]" value="{{ $oldQuantities[$index] ?? 1 }}" min="1" data-quantity-input required>
                        </label>

                        <div class="row-total">
                            <span>Subtotal</span>
                            <strong data-line-total>Rp 0</strong>
                        </div>

                        <button class="remove-row" type="button" data-remove-sale-row>Hapus</button>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="checkout-grid">
            <div class="form-card">
                <div class="form-header compact">
                    <div>
                        <span class="eyebrow">Pembayaran</span>
                        <h3>Diskon dan pembayaran</h3>
                    </div>
                </div>

                <label>
                    Diskon
                    <input type="number" name="discount" value="{{ old('discount', 0) }}" min="0" step="100" data-discount-input>
                </label>

                <label>
                    Pajak / Biaya Lain
                    <input type="number" name="tax" value="{{ old('tax', 0) }}" min="0" step="100" data-tax-input>
                </label>

                <label>
                    Uang Dibayar
                    <input type="number" name="paid_amount" value="{{ old('paid_amount', 0) }}" min="0" step="100" data-paid-input required>
                </label>
            </div>

            <aside class="checkout-card">
                <span class="eyebrow">Ringkasan</span>
                <h3>Total transaksi</h3>
                <div>
                    <span>Subtotal</span>
                    <strong data-subtotal>Rp 0</strong>
                </div>
                <div>
                    <span>Total Akhir</span>
                    <strong data-grand-total>Rp 0</strong>
                </div>
                <div>
                    <span>Kembalian</span>
                    <strong data-change-total>Rp 0</strong>
                </div>
                <button class="primary-button full" type="submit">Simpan Transaksi</button>
            </aside>
        </section>
    </form>

    <template id="sale-row-template">
        <div class="sale-item-row" data-sale-row>
            <label>
                Produk
                <select name="product_ids[]" data-product-select required>
                    <option value="" data-price="0" data-stock="0">Pilih produk</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->selling_price }}" data-stock="{{ $product->stock }}">
                            {{ $product->name }} - Rp {{ number_format($product->selling_price, 0, ',', '.') }} (stok {{ $product->stock }})
                        </option>
                    @endforeach
                </select>
            </label>
            <label>
                Qty
                <input type="number" name="quantities[]" value="1" min="1" data-quantity-input required>
            </label>
            <div class="row-total">
                <span>Subtotal</span>
                <strong data-line-total>Rp 0</strong>
            </div>
            <button class="remove-row" type="button" data-remove-sale-row>Hapus</button>
        </div>
    </template>
@endsection

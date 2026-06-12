<form class="form-card" method="POST" action="{{ $action }}">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="form-header">
        <div>
            <span class="eyebrow">Form produk</span>
            <h3>Informasi katalog barang</h3>
        </div>
        <p>Lengkapi data SKU, harga, stok, dan status produk agar modul transaksi membaca data yang benar.</p>
    </div>

    <div class="form-grid">
        <label>
            SKU Produk
            <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" placeholder="PRD-001" required>
        </label>

        <label>
            Nama Produk
            <input type="text" name="name" value="{{ old('name', $product->name) }}" placeholder="Nama barang" required>
        </label>

        <label>
            Kategori
            <input type="text" name="category" value="{{ old('category', $product->category) }}" placeholder="Elektronik, ATK, Fashion">
        </label>

        <label>
            Satuan
            <input type="text" name="unit" value="{{ old('unit', $product->unit ?? 'pcs') }}" required>
        </label>

        <label>
            Harga Modal
            <input type="number" name="purchase_price" value="{{ old('purchase_price', $product->purchase_price ?? 0) }}" min="0" step="100">
        </label>

        <label>
            Harga Jual
            <input type="number" name="selling_price" value="{{ old('selling_price', $product->selling_price) }}" min="0" step="100" required>
        </label>

        <label>
            Stok
            <input type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" min="0" required>
        </label>

        <label class="check-line form-check">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->exists ? $product->is_active : true))>
            Produk aktif dan bisa dijual
        </label>
    </div>

    <label>
        Deskripsi
        <textarea name="description" rows="4" placeholder="Catatan singkat produk">{{ old('description', $product->description) }}</textarea>
    </label>

    <div class="form-actions">
        <a class="secondary-button" href="{{ route('produk.index') }}">Batal</a>
        <button class="primary-button" type="submit">Simpan Produk</button>
    </div>
</form>

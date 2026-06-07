<form class="form-card" method="POST" action="{{ $action }}">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="form-grid">
        <label>
            Nama Pelanggan
            <input type="text" name="name" value="{{ old('name', $customer->name) }}" placeholder="Nama customer" required>
        </label>

        <label>
            No. Telepon
            <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" placeholder="08xxxxxxxxxx">
        </label>

        <label>
            Email
            <input type="email" name="email" value="{{ old('email', $customer->email) }}" placeholder="customer@email.com">
        </label>

        <label>
            Tipe Pelanggan
            <select name="type" required>
                @foreach (['Reguler', 'Reseller', 'Kampus', 'Prioritas'] as $type)
                    <option value="{{ $type }}" @selected(old('type', $customer->type ?? 'Reguler') === $type)>{{ $type }}</option>
                @endforeach
            </select>
        </label>
    </div>

    <label>
        Alamat
        <textarea name="address" rows="3" placeholder="Alamat lengkap">{{ old('address', $customer->address) }}</textarea>
    </label>

    <label>
        Catatan
        <textarea name="notes" rows="3" placeholder="Preferensi, kebiasaan belanja, atau catatan lainnya">{{ old('notes', $customer->notes) }}</textarea>
    </label>

    <div class="form-actions">
        <a class="secondary-button" href="{{ route('pelanggan.index') }}">Batal</a>
        <button class="primary-button" type="submit">Simpan Pelanggan</button>
    </div>
</form>

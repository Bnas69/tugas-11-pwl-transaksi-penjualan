# SalesLab PWL - Manajemen Transaksi Penjualan

Project Laravel untuk tugas Pemrograman Web Lanjut Pertemuan 11.

## Identitas

- Nama: Septian Dwi Saputra
- NIM: 411232056
- Program Studi: Teknik Informatika
- Universitas: Universitas Dian Nusantara

## Modul yang tersedia

1. Login admin
2. Dashboard ringkasan penjualan
3. Master data produk
4. Master data pelanggan
5. Modul transaksi penjualan
6. Detail invoice dan cetak invoice
7. Export laporan transaksi ke Excel (.xlsx), CSV (.csv), dan SQL (.sql)

## Catatan implementasi export

Fitur export ditempatkan pada modul Penjualan karena modul ini merupakan modul transaksi utama.
Export dibuat server-side dari database agar data yang diunduh sesuai dengan data transaksi yang tersimpan dan mengikuti filter pencarian.

- Excel: untuk laporan yang dibuka di Microsoft Excel atau LibreOffice.
- CSV: untuk pertukaran data ringan.
- SQL: untuk backup sederhana data transaksi penjualan dan detail item.

## Cara menjalankan lokal

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run dev
php artisan serve
```

Akun demo seeder:

```text
Email: admin@septian.test
Password: password
```

## Catatan asset tampilan

Project ini sudah memakai asset statis di `public/css/app.css` dan `public/js/app.js`, sehingga tidak wajib menjalankan Vite saat upload ke hosting. Jika Anda tetap ingin mengembangkan tampilan melalui `resources/css/app.css` atau `resources/js/app.js`, salin hasil perubahannya ke folder `public/css` dan `public/js` sebelum upload.

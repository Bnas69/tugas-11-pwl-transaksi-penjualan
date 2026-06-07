# SalesLab PWL - Manajemen Transaksi Penjualan

Project Laravel untuk tugas Pemrograman Web Lanjut Pertemuan 11.

## Identitas

- Nama: Septian Dwi Saputra
- NIM: 411232056
- Program Studi: Teknik Informatika
- Universitas: Universitas Dian Nusantara

## Modul<img width="1440" height="900" alt="Screenshot 2026-06-07 at 17 13 26" src="https://github.com/user-attachments/assets/e4170fbb-deeb-4a17-9658-40dfcf6c1598" />
<img width="1440" height="900" alt="Screenshot 2026-06-07 at 17 13 39" src="https://github.com/user-attachments/assets/689cabcf-3d7b-45a6-980c-a2e0ae3b3824" />
<img width="1440" height="900" alt="Screenshot 2026-06-07 at 17 14 08" src="https://github.com/user-attachments/assets/3d6d1fd4-e448-4585-8a36-597d8b5fd907" />
<img width="1440" height="900" alt="Screenshot 2026-06-07 at 17 14 08" src="https://github.com/user-attachments/assets/9c059ff6-0740-44e7-b40e-de52ef3f4c84" />


1. Login admin
2. Dashboard ringkasan penjualan
3. Master data produk
4. Master data pelanggan
5. Modul transaksi penjualan
6. Detail invoice dan cetak invoice
7. Export laporan transaksi ke Excel (.xlsx), CSV (.csv), dan SQL (.sql)

## Update PT 11 Pak Sandy

Fitur export ditempatkan pada modul Penjualan karena modul ini merupakan modul transaksi utama.
Export dibuat server-side dari database agar data yang diunduh sesuai dengan data transaksi yang tersimpan dan mengikuti filter pencarian.

- Excel: untuk laporan yang dibuka di Microsoft Excel atau LibreOffice.
- CSV: untuk pertukaran data ringan.
- SQL: untuk backup sederhana data transaksi penjualan dan detail item.

## lokal

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run dev
php artisan serve
```

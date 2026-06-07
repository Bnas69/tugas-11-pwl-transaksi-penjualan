<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@septian.test'],
            [
                'name' => 'Septian Dwi Saputra',
                'password' => 'password',
                'role' => 'admin',
            ]
        );

        $products = collect([
            ['sku' => 'ATK-001', 'name' => 'Notebook Kampus A5', 'category' => 'ATK', 'purchase_price' => 12000, 'selling_price' => 18000, 'stock' => 42, 'unit' => 'pcs', 'description' => 'Buku catatan untuk kebutuhan kuliah.'],
            ['sku' => 'ELK-014', 'name' => 'Mouse Wireless Slim', 'category' => 'Elektronik', 'purchase_price' => 65000, 'selling_price' => 89000, 'stock' => 16, 'unit' => 'pcs', 'description' => 'Mouse ringan untuk kerja dan tugas.'],
            ['sku' => 'FNB-022', 'name' => 'Kopi Susu Botol', 'category' => 'Minuman', 'purchase_price' => 9000, 'selling_price' => 15000, 'stock' => 28, 'unit' => 'botol', 'description' => 'Produk harian yang cepat terjual.'],
            ['sku' => 'BAG-007', 'name' => 'Totebag Canvas Undira', 'category' => 'Merchandise', 'purchase_price' => 27000, 'selling_price' => 45000, 'stock' => 8, 'unit' => 'pcs', 'description' => 'Totebag simple untuk mahasiswa.'],
            ['sku' => 'ATK-018', 'name' => 'Pulpen Gel Hitam', 'category' => 'ATK', 'purchase_price' => 3000, 'selling_price' => 6000, 'stock' => 5, 'unit' => 'pcs', 'description' => 'Stok dibuat rendah untuk contoh notifikasi dashboard.'],
        ])->map(fn (array $product) => Product::updateOrCreate(
            ['sku' => $product['sku']],
            $product + ['is_active' => true]
        ));

        $customers = collect([
            ['name' => 'Raka Pratama', 'phone' => '081234567890', 'email' => 'raka@example.com', 'type' => 'Reguler', 'address' => 'Kebon Jeruk, Jakarta Barat', 'notes' => 'Sering membeli ATK.'],
            ['name' => 'Dina Maharani', 'phone' => '081298761234', 'email' => 'dina@example.com', 'type' => 'Kampus', 'address' => 'Tanjung Duren, Jakarta Barat', 'notes' => 'Butuh invoice untuk organisasi kampus.'],
            ['name' => 'Bima Reseller', 'phone' => '087777888999', 'email' => 'bima@example.com', 'type' => 'Reseller', 'address' => 'Cengkareng, Jakarta Barat', 'notes' => 'Biasanya ambil banyak produk merchandise.'],
        ])->map(fn (array $customer) => Customer::updateOrCreate(
            ['email' => $customer['email']],
            $customer
        ));

        if (! Sale::where('invoice_number', 'INV-DEMO-0001')->exists()) {
            $firstProduct = $products->firstWhere('sku', 'ATK-001');
            $secondProduct = $products->firstWhere('sku', 'FNB-022');
            $subtotal = ($firstProduct->selling_price * 2) + ($secondProduct->selling_price * 3);

            $sale = Sale::create([
                'invoice_number' => 'INV-DEMO-0001',
                'customer_id' => $customers->first()->id,
                'user_id' => $admin->id,
                'sale_date' => now()->toDateString(),
                'subtotal' => $subtotal,
                'discount' => 3000,
                'tax' => 0,
                'grand_total' => $subtotal - 3000,
                'paid_amount' => 100000,
                'change_amount' => 100000 - ($subtotal - 3000),
                'payment_method' => 'Tunai',
                'status' => 'Lunas',
                'notes' => 'Transaksi contoh dari seed data.',
            ]);

            foreach ([[$firstProduct, 2], [$secondProduct, 3]] as [$product, $quantity]) {
                $sale->items()->create([
                    'product_id' => $product->id,
                    'product_sku' => $product->sku,
                    'product_name' => $product->name,
                    'quantity' => $quantity,
                    'price' => $product->selling_price,
                    'line_total' => $product->selling_price * $quantity,
                ]);

                $product->decrement('stock', $quantity);
            }
        }
    }
}

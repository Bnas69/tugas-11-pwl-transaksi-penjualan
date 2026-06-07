<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('cari')->toString();

        $products = Product::withCount('saleItems')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(8)
            ->withQueryString();

        return view('products.index', compact('products', 'search'));
    }

    public function create()
    {
        return view('products.create', [
            'product' => new Product([
                'unit' => 'pcs',
                'is_active' => true,
            ]),
        ]);
    }

    public function store(Request $request)
    {
        Product::create($this->validatedProduct($request));

        return redirect()->route('produk.index')->with('success', 'Produk baru berhasil disimpan.');
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $product->update($this->validatedProduct($request, $product));

        return redirect()->route('produk.index')->with('success', 'Data produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        if ($product->saleItems()->exists()) {
            $product->update(['is_active' => false]);

            return back()->with('success', 'Produk pernah dipakai transaksi, jadi statusnya dinonaktifkan.');
        }

        $product->delete();

        return back()->with('success', 'Produk berhasil dihapus.');
    }

    private function validatedProduct(Request $request, ?Product $product = null): array
    {
        $data = $request->validate([
            'sku' => ['required', 'max:60', Rule::unique('products', 'sku')->ignore($product)],
            'name' => ['required', 'max:160'],
            'category' => ['nullable', 'max:120'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'unit' => ['required', 'max:24'],
            'description' => ['nullable', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['purchase_price'] = $data['purchase_price'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}

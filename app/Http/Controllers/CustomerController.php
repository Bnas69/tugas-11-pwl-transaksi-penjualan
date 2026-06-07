<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('cari')->toString();

        $customers = Customer::withCount('sales')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('type', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(8)
            ->withQueryString();

        return view('customers.index', compact('customers', 'search'));
    }

    public function create()
    {
        return view('customers.create', [
            'customer' => new Customer(['type' => 'Reguler']),
        ]);
    }

    public function store(Request $request)
    {
        Customer::create($this->validatedCustomer($request));

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan baru berhasil disimpan.');
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $customer->update($this->validatedCustomer($request, $customer));

        return redirect()->route('pelanggan.index')->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->sales()->exists()) {
            return back()->withErrors('Pelanggan sudah memiliki transaksi, jadi tidak dihapus agar riwayat tetap aman.');
        }

        $customer->delete();

        return back()->with('success', 'Pelanggan berhasil dihapus.');
    }

    private function validatedCustomer(Request $request, ?Customer $customer = null): array
    {
        return $request->validate([
            'name' => ['required', 'max:160'],
            'phone' => ['nullable', 'max:40'],
            'email' => ['nullable', 'email', 'max:160', Rule::unique('customers', 'email')->ignore($customer)],
            'type' => ['required', 'max:40'],
            'address' => ['nullable', 'max:1000'],
            'notes' => ['nullable', 'max:1000'],
        ]);
    }
}

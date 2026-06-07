<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('dashboard.index', [
            'productCount' => Product::count(),
            'customerCount' => Customer::count(),
            'transactionCount' => Sale::count(),
            'todayRevenue' => Sale::whereDate('sale_date', today())->sum('grand_total'),
            'monthRevenue' => Sale::whereMonth('sale_date', now()->month)
                ->whereYear('sale_date', now()->year)
                ->sum('grand_total'),
            'latestSales' => Sale::with('customer')->latest()->take(5)->get(),
            'lowStockProducts' => Product::where('is_active', true)
                ->where('stock', '<=', 5)
                ->orderBy('stock')
                ->take(5)
                ->get(),
            'bestProducts' => SaleItem::select('product_name')
                ->selectRaw('SUM(quantity) as sold_qty, SUM(line_total) as total_sales')
                ->groupBy('product_name')
                ->orderByDesc('sold_qty')
                ->take(5)
                ->get(),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Stock;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function owner()
    {
        $totalBranches = Branch::count();
        $totalProducts = Product::count();
        $totalSales = Sale::count();
        $totalRevenue = Sale::sum('total_price');

        // Stok menipis (semua cabang)
        $lowStocks = Stock::with(['product', 'branch'])
            ->whereColumn('quantity', '<=', 'products.minimum_stock')
            ->join('products', 'stocks.product_id', '=', 'products.id')
            ->select('stocks.*')
            ->limit(10)
            ->get();

        // Penjualan per cabang
        $salesPerBranch = Branch::withCount('sales')
            ->withSum('sales', 'total_price')
            ->get();

        // Cashier Performance (This Month, All Branches)
        $cashierPerformances = \App\Models\User::where('role', 'kasir')
            ->with('branch')
            ->withCount(['sales as transactions_count' => function ($query) {
                $query->whereMonth('transaction_date', now()->month)
                      ->whereYear('transaction_date', now()->year);
            }])
            ->withSum(['sales as total_revenue' => function ($query) {
                $query->whereMonth('transaction_date', now()->month)
                      ->whereYear('transaction_date', now()->year);
            }], 'total_price')
            ->orderByDesc('total_revenue')
            ->take(10) // Top 10
            ->get();

        return view('owner.dashboard', compact(
            'totalBranches', 'totalProducts', 'totalSales', 'totalRevenue', 'lowStocks', 'salesPerBranch', 'cashierPerformances'
        ));
    }

    public function kepalaCabang()
    {
        $branchId = Auth::user()->branch_id;

        $totalProducts = Stock::where('branch_id', $branchId)->where('quantity', '>', 0)->count();

        $lowStocks = Stock::with('product')
            ->where('branch_id', $branchId)
            ->whereColumn('quantity', '<=', 'products.minimum_stock')
            ->join('products', 'stocks.product_id', '=', 'products.id')
            ->select('stocks.*')
            ->get();

        $salesToday = Sale::where('branch_id', $branchId)
            ->whereDate('transaction_date', today())
            ->count();

        $revenueTodayVal = Sale::where('branch_id', $branchId)
            ->whereDate('transaction_date', today())
            ->sum('total_price');

        // Cashier Performance (This Month)
        $cashierPerformances = \App\Models\User::where('branch_id', $branchId)
            ->where('role', 'kasir')
            ->withCount(['sales as transactions_count' => function ($query) {
                $query->whereMonth('transaction_date', now()->month)
                      ->whereYear('transaction_date', now()->year);
            }])
            ->withSum(['sales as total_revenue' => function ($query) {
                $query->whereMonth('transaction_date', now()->month)
                      ->whereYear('transaction_date', now()->year);
            }], 'total_price')
            ->orderByDesc('total_revenue')
            ->get();

        return view('kepala-cabang.dashboard', compact('totalProducts', 'lowStocks', 'salesToday', 'revenueTodayVal', 'cashierPerformances'));
    }

    public function kasir()
    {
        $branchId = Auth::user()->branch_id;

        $salesToday = Sale::where('branch_id', $branchId)
            ->whereDate('transaction_date', today())
            ->count();

        $revenueTodayVal = Sale::where('branch_id', $branchId)
            ->whereDate('transaction_date', today())
            ->sum('total_price');

        return view('kasir.dashboard', compact('salesToday', 'revenueTodayVal'));
    }
}

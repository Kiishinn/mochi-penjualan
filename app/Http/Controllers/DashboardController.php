<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Stock;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function owner(\Illuminate\Http\Request $request)
    {
        // Filters for Performa Cabang
        $pcStartDate = $request->get('pc_start_date');
        $pcEndDate = $request->get('pc_end_date');
        
        // Filters for Top Kasir
        $kasirBranchId = $request->get('kasir_branch_id');
        $kasirStartDate = $request->get('kasir_start_date');
        $kasirEndDate = $request->get('kasir_end_date');
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

        // Penjualan per cabang (dengan filter)
        $salesQuery = function ($query) use ($pcStartDate, $pcEndDate) {
            if ($pcStartDate && $pcEndDate) {
                $query->whereBetween('transaction_date', [$pcStartDate . ' 00:00:00', $pcEndDate . ' 23:59:59']);
            }
        };

        $branchQuery = Branch::withCount(['sales' => $salesQuery])
            ->withSum(['sales' => $salesQuery], 'total_price');
            
        $salesPerBranch = $branchQuery->get();

        // Cashier Performance (dengan filter)
        $cashierQuery = \App\Models\User::where('role', 'kasir')->with('branch');
        
        if ($kasirBranchId) {
            $cashierQuery->where('branch_id', $kasirBranchId);
        }

        $cashierSalesQuery = function ($query) use ($kasirStartDate, $kasirEndDate) {
            if ($kasirStartDate && $kasirEndDate) {
                $query->whereBetween('transaction_date', [$kasirStartDate . ' 00:00:00', $kasirEndDate . ' 23:59:59']);
            } else {
                // Default to this month if no date filter is applied
                $query->whereMonth('transaction_date', now()->month)
                      ->whereYear('transaction_date', now()->year);
            }
        };

        $cashierPerformances = $cashierQuery
            ->withCount(['sales as transactions_count' => $cashierSalesQuery])
            ->withSum(['sales as total_revenue' => $cashierSalesQuery], 'total_price')
            ->orderByDesc('total_revenue')
            ->take(10) // Top 10
            ->get();

        $branches = Branch::all();

        return view('owner.dashboard', compact(
            'totalBranches', 'totalProducts', 'totalSales', 'totalRevenue', 'lowStocks', 'salesPerBranch', 'cashierPerformances', 'branches'
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

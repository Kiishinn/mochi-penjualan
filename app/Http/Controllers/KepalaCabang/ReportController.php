<?php

namespace App\Http\Controllers\KepalaCabang;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function sales(Request $request) {
        $branchId = Auth::user()->branch_id;
        $query = Sale::with(['user', 'details.product'])->where('branch_id', $branchId);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('transaction_date', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        $sales = $query->latest()->get();
        $totalRevenue = $sales->sum('total_price');
        $totalTransactions = $sales->count();

        $topProducts = SaleDetail::selectRaw('product_id, SUM(quantity) as total_qty')
            ->whereIn('sale_id', (clone $query)->select('id'))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->with('product')
            ->get();

        return view('kepala-cabang.reports.sales', compact('sales', 'totalRevenue', 'totalTransactions', 'topProducts'));
    }

    public function stocks() {
        $branchId = Auth::user()->branch_id;
        $stocks = Stock::with(['product.category', 'product.unit'])
            ->where('branch_id', $branchId)->get();
        return view('kepala-cabang.reports.stocks', compact('stocks'));
    }

    public function transactions() {
        $branchId = Auth::user()->branch_id;
        $sales = Sale::with(['user', 'details.product'])
            ->where('branch_id', $branchId)->latest()->paginate(10);
        return view('kepala-cabang.sales.index', compact('sales'));
    }
}

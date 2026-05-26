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

    public function stocks(Request $request) {
        $branchId = Auth::user()->branch_id;
        
        $query = Stock::with(['product.category', 'product.unit'])
            ->select('stocks.*')
            ->join('products', 'stocks.product_id', '=', 'products.id')
            ->where('stocks.branch_id', $branchId);
            
        if ($request->filled('search')) {
            $query->where('products.name', 'like', '%' . $request->search . '%');
        }
        
        if ($request->filled('status')) {
            if ($request->status == 'habis') {
                $query->where('stocks.quantity', 0);
            } elseif ($request->status == 'menipis') {
                $query->where('stocks.quantity', '>', 0)
                      ->whereRaw('stocks.quantity <= products.minimum_stock');
            } elseif ($request->status == 'aman') {
                $query->whereRaw('stocks.quantity > products.minimum_stock');
            }
        }
        
        $stocks = $query->paginate(10);
        return view('kepala-cabang.reports.stocks', compact('stocks'));
    }

    public function transactions(Request $request) {
        $branchId = Auth::user()->branch_id;
        $query = Sale::with(['user', 'details.product'])
            ->where('branch_id', $branchId);
            
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('receipt_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($q2) use ($request) {
                      $q2->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }
        
        $sales = $query->latest()->paginate(10);
        return view('kepala-cabang.sales.index', compact('sales'));
    }

    public function showTransaction(Sale $sale) {
        if ($sale->branch_id !== Auth::user()->branch_id) {
            abort(403, 'Unauthorized action.');
        }
        $sale->load(['branch', 'user', 'details.product']);
        $backUrl = route('kepala-cabang.transactions.index');
        return view('kasir.transactions.show', ['transaction' => $sale, 'backUrl' => $backUrl]);
    }

    public function bestSellers(Request $request) {
        $branchId = Auth::user()->branch_id;
        $query = Sale::where('branch_id', $branchId);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('transaction_date', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        $topProducts = SaleDetail::selectRaw('product_id, SUM(quantity) as total_qty')
            ->whereIn('sale_id', $query->select('id'))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->with(['product.category', 'product.unit'])
            ->paginate(20);

        return view('kepala-cabang.reports.best-sellers', compact('topProducts'));
    }

    public function lowStock() {
        $branchId = Auth::user()->branch_id;
        
        // Find products where stock is <= minimum_stock
        $lowStocks = Stock::with(['product.category', 'product.unit'])
            ->where('branch_id', $branchId)
            ->whereHas('product', function ($q) {
                // Cannot easily whereColumn across tables in Eloquent relationships like this directly, 
                // so we use a join or raw query.
            })
            ->join('products', 'stocks.product_id', '=', 'products.id')
            ->whereColumn('stocks.quantity', '<=', 'products.minimum_stock')
            ->select('stocks.*') // Keep stock model
            ->orderBy('stocks.quantity', 'asc')
            ->get();
            
        return view('kepala-cabang.reports.low-stock', compact('lowStocks'));
    }

    public function stockCard($product_id) {
        $branchId = Auth::user()->branch_id;
        $product = \App\Models\Product::findOrFail($product_id);
        
        $events = collect();
        
        // Stock Ins
        \App\Models\StockIn::where('branch_id', $branchId)->where('product_id', $product_id)
            ->get()->each(function ($item) use (&$events) {
                $events->push(['date' => $item->date, 'type' => 'Stok Masuk', 'qty' => $item->quantity, 'ref' => 'Supplier: ' . ($item->supplier->name ?? '-')]);
            });
            
        // Stock Outs
        \App\Models\StockOut::where('branch_id', $branchId)->where('product_id', $product_id)
            ->get()->each(function ($item) use (&$events) {
                $events->push(['date' => $item->date, 'type' => 'Stok Keluar', 'qty' => -$item->quantity, 'ref' => 'Alasan: ' . ($item->reason ?? '-')]);
            });
            
        // Transfers Out
        \App\Models\StockTransfer::where('from_branch_id', $branchId)->where('product_id', $product_id)->whereIn('status', ['approved', 'received'])
            ->get()->each(function ($item) use (&$events) {
                $events->push(['date' => $item->updated_at, 'type' => 'Transfer Keluar', 'qty' => -$item->final_quantity, 'ref' => 'Ke Cabang: ' . ($item->toBranch->name ?? '-')]);
            });
            
        // Transfers In
        \App\Models\StockTransfer::where('to_branch_id', $branchId)->where('product_id', $product_id)->where('status', 'received')
            ->get()->each(function ($item) use (&$events) {
                $events->push(['date' => $item->updated_at, 'type' => 'Transfer Masuk', 'qty' => $item->final_quantity, 'ref' => 'Dari Cabang: ' . ($item->fromBranch->name ?? '-')]);
            });
            
        // Sales
        SaleDetail::whereHas('sale', function($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        })->where('product_id', $product_id)->get()->each(function ($item) use (&$events) {
            $events->push(['date' => $item->sale->transaction_date, 'type' => 'Penjualan', 'qty' => -$item->quantity, 'ref' => 'Invoice: ' . $item->sale->invoice_number]);
        });
        
        // Returns
        \App\Models\ReturnItem::where('branch_id', $branchId)
            ->where('status', 'approved')
            ->where('product_id', $product_id)
            ->get()->each(function ($item) use (&$events) {
            // If condition is good, usually stock is returned
            if ($item->item_condition === 'good' && $item->return_type !== 'exchange') {
                $events->push(['date' => $item->updated_at, 'type' => 'Retur Masuk', 'qty' => $item->quantity, 'ref' => 'Retur Invoice: ' . ($item->sale->invoice_number ?? '-')]);
            }
        });
        
        $sortedEvents = $events->sortBy('date')->values();
        
        $actualStock = \App\Models\Stock::where('branch_id', $branchId)->where('product_id', $product_id)->value('quantity') ?? 0;
        $sumEvents = $sortedEvents->sum('qty');
        $startingBalance = $actualStock - $sumEvents;
        
        // Calculate running balance starting from the true initial balance
        $balance = $startingBalance;
        $sortedEvents = $sortedEvents->map(function($event) use (&$balance) {
            $balance += $event['qty'];
            $event['balance'] = $balance;
            return $event;
        });

        return view('kepala-cabang.reports.stock-card', compact('product', 'sortedEvents'));
    }
}

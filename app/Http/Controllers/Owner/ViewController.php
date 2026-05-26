<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ReturnItem;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\StockTransfer;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\Branch;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function categories(Request $request) { 
        $query = Category::query();
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        return view('owner.categories.index', ['categories' => $query->orderBy('name')->paginate(10)]); 
    }
    public function units(Request $request) { 
        $query = Unit::query();
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('symbol', 'like', '%' . $request->search . '%');
        }
        return view('owner.units.index', ['units' => $query->orderBy('name')->paginate(10)]); 
    }
    public function suppliers(Request $request) { 
        $query = Supplier::query();
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('contact_person', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
        }
        return view('owner.suppliers.index', ['suppliers' => $query->orderBy('name')->paginate(10)]); 
    }
    public function products(Request $request) { 
        $query = Product::with(['category', 'unit']);
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        return view('owner.products.index', ['products' => $query->orderBy('name')->paginate(10)]); 
    }

    public function stockIns(Request $request) {
        $query = StockIn::with(['branch', 'product', 'supplier', 'creator']);
        if ($request->filled('search')) {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })->orWhere('notes', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        $stockIns = $query->latest()->paginate(10);
        $branches = Branch::orderBy('name')->get();
        return view('owner.stock-ins.index', compact('stockIns', 'branches'));
    }

    public function stockOuts(Request $request) {
        $query = StockOut::with(['branch', 'product', 'creator']);
        if ($request->filled('search')) {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })->orWhere('notes', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        $stockOuts = $query->latest()->paginate(10);
        $branches = Branch::orderBy('name')->get();
        return view('owner.stock-outs.index', compact('stockOuts', 'branches'));
    }

    public function stockTransfers(Request $request) {
        $query = StockTransfer::with(['fromBranch', 'toBranch', 'product', 'creator']);
        if ($request->filled('search')) {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })->orWhere('notes', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('branch_id')) {
            $query->where(function($q) use ($request) {
                $q->where('from_branch_id', $request->branch_id)
                  ->orWhere('to_branch_id', $request->branch_id);
            });
        }
        $transfers = $query->latest()->paginate(10);
        $branches = Branch::orderBy('name')->get();
        return view('owner.stock-transfers.index', compact('transfers', 'branches'));
    }

    public function sales(Request $request) {
        $query = Sale::with(['branch', 'user', 'details']);
        if ($request->filled('search')) {
            $query->where('receipt_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%');
                  });
        }
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        $sales = $query->latest()->paginate(10);
        $branches = Branch::orderBy('name')->get();
        return view('owner.sales.index', compact('sales', 'branches'));
    }

    public function showTransaction(Sale $sale) {
        $sale->load(['branch', 'user', 'details.product']);
        $backUrl = route('transactions.index');
        return view('kasir.transactions.show', ['transaction' => $sale, 'backUrl' => $backUrl]);
    }

    public function returnItems(Request $request) {
        $query = ReturnItem::with(['sale', 'product', 'branch', 'user']);
        if ($request->filled('search')) {
            $query->where('reason', 'like', '%' . $request->search . '%')
                  ->orWhereHas('sale', function($q) use ($request) {
                      $q->where('receipt_number', 'like', '%' . $request->search . '%');
                  });
        }
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        $returnItems = $query->latest()->paginate(10);
        $branches = Branch::orderBy('name')->get();
        return view('owner.returns.index', compact('returnItems', 'branches'));
    }

    public function reportSales(Request $request) {
        $baseQuery = Sale::query();

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $baseQuery->whereBetween('transaction_date', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }
        if ($request->filled('branch_id')) {
            $baseQuery->where('branch_id', $request->branch_id);
        }

        $totalRevenue = (clone $baseQuery)->sum('total_price');
        $totalTransactions = (clone $baseQuery)->count();

        // Chart 1: Trend Penjualan
        $trendData = (clone $baseQuery)
            ->selectRaw('DATE(transaction_date) as date, SUM(total_price) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        $chartDates = $trendData->pluck('date');
        $chartTotals = $trendData->pluck('total');

        // Chart 2: Kontribusi Cabang
        $branchData = clone $baseQuery;
        $branchData = $branchData->join('branches', 'sales.branch_id', '=', 'branches.id')
            ->selectRaw('branches.name as branch_name, SUM(sales.total_price) as total')
            ->groupBy('branches.name')
            ->get();
        $pieLabels = $branchData->pluck('branch_name');
        $pieData = $branchData->pluck('total');

        // Produk terlaris
        $topProducts = \App\Models\SaleDetail::selectRaw('product_id, SUM(quantity) as total_qty')
            ->whereIn('sale_id', (clone $baseQuery)->select('id'))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->with('product.category')
            ->get();

        $sales = (clone $baseQuery)->with(['branch', 'user', 'details.product'])->latest()->paginate(10);
        $branches = \App\Models\Branch::orderBy('name')->get();

        return view('owner.reports.sales', compact(
            'sales', 'totalRevenue', 'totalTransactions', 'topProducts', 'branches',
            'chartDates', 'chartTotals', 'pieLabels', 'pieData'
        ));
    }

    

    public function reportStocks(Request $request) {
        $query = Stock::with(['branch', 'product.category', 'product.unit'])
            ->select('stocks.*')
            ->join('products', 'stocks.product_id', '=', 'products.id');

        if ($request->filled('branch_id')) {
            $query->where('stocks.branch_id', $request->branch_id);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('products.name', 'like', '%' . $request->search . '%');
            });
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

        // Metrics
        $totalAset = (clone $query)->sum(\Illuminate\Support\Facades\DB::raw('stocks.quantity * products.purchase_price'));
        $totalSku = (clone $query)->count('stocks.id');
        
        // For habis and menipis totals, we base them on the current query without additional status filters to show exact numbers of the *filtered branch*.
        // Wait, if the query already has status filter, count will just reflect the filtered items. This is standard behavior.
        $totalHabis = (clone $query)->where('stocks.quantity', 0)->count('stocks.id');
        $totalMenipis = (clone $query)->where('stocks.quantity', '>', 0)->whereRaw('stocks.quantity <= products.minimum_stock')->count('stocks.id');

        $stocks = $query->orderBy('stocks.branch_id')->paginate(10);
        $branches = \App\Models\Branch::orderBy('name')->get();

        return view('owner.reports.stocks', compact('stocks', 'branches', 'totalSku', 'totalHabis', 'totalMenipis', 'totalAset'));
    }

    

    public function shifts(Request $request)
    {
        $query = \App\Models\Shift::with(['user', 'branch']);
        if ($request->filled('search')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        $shifts = $query->latest('start_time')->paginate(15);
        $branches = Branch::orderBy('name')->get();
            
        return view('owner.shifts.index', compact('shifts', 'branches'));
    }

    public function bestSellers(Request $request) {
        $query = \App\Models\Sale::query();

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('transaction_date', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }
        
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $topProducts = \App\Models\SaleDetail::selectRaw('product_id, SUM(quantity) as total_qty')
            ->whereIn('sale_id', clone $query->select('id'))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->with(['product.category', 'product.unit'])
            ->paginate(20);
            
        $branches = \App\Models\Branch::orderBy('name')->get();

        return view('owner.reports.best-sellers', compact('topProducts', 'branches'));
    }

    public function lowStock(Request $request) {
        $query = \App\Models\Stock::with(['product.category', 'product.unit', 'branch'])
            ->join('products', 'stocks.product_id', '=', 'products.id')
            ->whereColumn('stocks.quantity', '<=', 'products.minimum_stock');

        if ($request->filled('branch_id')) {
            $query->where('stocks.branch_id', $request->branch_id);
        }

        $lowStocks = $query->select('stocks.*')
            ->orderBy('stocks.quantity', 'asc')
            ->get();
            
        $branches = \App\Models\Branch::orderBy('name')->get();
            
        return view('owner.reports.low-stock', compact('lowStocks', 'branches'));
    }

    public function stockCard($branch_id, $product_id) {
        $product = \App\Models\Product::findOrFail($product_id);
        $branch = \App\Models\Branch::findOrFail($branch_id);
        
        $events = collect();
        
        // Stock Ins
        \App\Models\StockIn::where('branch_id', $branch_id)->where('product_id', $product_id)
            ->get()->each(function ($item) use (&$events) {
                $events->push(['date' => $item->date, 'type' => 'Stok Masuk', 'qty' => $item->quantity, 'ref' => 'Supplier: ' . ($item->supplier->name ?? '-')]);
            });
            
        // Stock Outs
        \App\Models\StockOut::where('branch_id', $branch_id)->where('product_id', $product_id)
            ->get()->each(function ($item) use (&$events) {
                $events->push(['date' => $item->date, 'type' => 'Stok Keluar', 'qty' => -$item->quantity, 'ref' => 'Alasan: ' . ($item->reason ?? '-')]);
            });
            
        // Transfers Out
        \App\Models\StockTransfer::where('from_branch_id', $branch_id)->where('product_id', $product_id)->where('status', 'approved')
            ->get()->each(function ($item) use (&$events) {
                $events->push(['date' => $item->updated_at, 'type' => 'Transfer Keluar', 'qty' => -$item->quantity, 'ref' => 'Ke Cabang: ' . ($item->toBranch->name ?? '-')]);
            });
            
        // Transfers In
        \App\Models\StockTransfer::where('to_branch_id', $branch_id)->where('product_id', $product_id)->where('status', 'approved')
            ->get()->each(function ($item) use (&$events) {
                $events->push(['date' => $item->updated_at, 'type' => 'Transfer Masuk', 'qty' => $item->quantity, 'ref' => 'Dari Cabang: ' . ($item->fromBranch->name ?? '-')]);
            });
            
        // Sales
        \App\Models\SaleDetail::whereHas('sale', function($q) use ($branch_id) {
            $q->where('branch_id', $branch_id);
        })->where('product_id', $product_id)->get()->each(function ($item) use (&$events) {
            $events->push(['date' => $item->sale->transaction_date, 'type' => 'Penjualan', 'qty' => -$item->quantity, 'ref' => 'Invoice: ' . $item->sale->invoice_number]);
        });
        
        // Returns
        \App\Models\ReturnItem::where('branch_id', $branch_id)
            ->where('status', 'approved')
            ->where('product_id', $product_id)
            ->get()->each(function ($item) use (&$events) {
            if ($item->item_condition === 'good' && $item->return_type !== 'exchange') {
                $events->push(['date' => $item->updated_at, 'type' => 'Retur Masuk', 'qty' => $item->quantity, 'ref' => 'Retur Invoice: ' . ($item->sale->invoice_number ?? '-')]);
            }
        });
        
        $sortedEvents = $events->sortBy('date')->values();
        
        $balance = 0;
        $sortedEvents = $sortedEvents->map(function($event) use (&$balance) {
            $balance += $event['qty'];
            $event['balance'] = $balance;
            return $event;
        });

        return view('owner.reports.stock-card', compact('product', 'branch', 'sortedEvents'));
    }
}


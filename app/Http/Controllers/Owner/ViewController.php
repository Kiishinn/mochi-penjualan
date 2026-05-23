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
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function categories() { return view('owner.categories.index', ['categories' => Category::orderBy('name')->paginate(10)]); }
    public function units() { return view('owner.units.index', ['units' => Unit::orderBy('name')->paginate(10)]); }
    public function suppliers() { return view('owner.suppliers.index', ['suppliers' => Supplier::orderBy('name')->paginate(10)]); }
    public function products() { return view('owner.products.index', ['products' => Product::with(['category', 'unit'])->orderBy('name')->paginate(10)]); }

    public function stockIns() {
        $stockIns = StockIn::with(['branch', 'product', 'supplier', 'creator'])->latest()->paginate(10);
        return view('owner.stock-ins.index', compact('stockIns'));
    }

    public function stockOuts() {
        $stockOuts = StockOut::with(['branch', 'product', 'creator'])->latest()->paginate(10);
        return view('owner.stock-outs.index', compact('stockOuts'));
    }

    public function stockTransfers() {
        $transfers = StockTransfer::with(['fromBranch', 'toBranch', 'product', 'creator'])->latest()->paginate(10);
        return view('owner.stock-transfers.index', compact('transfers'));
    }

    public function sales() {
        $sales = Sale::with(['branch', 'user', 'details'])->latest()->paginate(10);
        return view('owner.sales.index', compact('sales'));
    }

    public function returnItems() {
        $returnItems = ReturnItem::with(['sale', 'product', 'branch', 'user'])->latest()->paginate(10);
        return view('owner.returns.index', compact('returnItems'));
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

    public function exportSalesCsv(Request $request) {
        $query = Sale::with(['branch', 'user', 'details.product']);
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('transaction_date', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        
        $sales = $query->orderBy('transaction_date')->get();
        
        $filename = "Laporan_Penjualan_" . date('Ymd_His') . ".csv";
        $handle = fopen('php://output', 'w');
        
        // Add BOM for Excel UTF-8
        fputs($handle, $bom =(chr(0xEF) . chr(0xBB) . chr(0xBF)));

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        fputcsv($handle, ['Tanggal', 'No Invoice', 'Cabang', 'Kasir', 'Total Harga', 'Tunai', 'Kembalian']);
        
        foreach ($sales as $sale) {
            fputcsv($handle, [
                $sale->transaction_date,
                $sale->invoice_number,
                $sale->branch->name ?? '-',
                $sale->user->name ?? '-',
                $sale->total_price,
                $sale->paid_amount,
                $sale->change_amount
            ]);
        }
        
        fclose($handle);
        exit;
    }

    public function reportStocks(Request $request) {
        $query = Stock::with(['branch', 'product.category', 'product.unit'])
            ->select('stocks.*')
            ->join('products', 'stocks.product_id', '=', 'products.id');

        if ($request->filled('branch_id')) {
            $query->where('stocks.branch_id', $request->branch_id);
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

    public function exportStocksCsv(Request $request) {
        $query = Stock::with(['branch', 'product.category', 'product.unit'])
            ->select('stocks.*')
            ->join('products', 'stocks.product_id', '=', 'products.id');

        if ($request->filled('branch_id')) {
            $query->where('stocks.branch_id', $request->branch_id);
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
        
        $stocks = $query->orderBy('stocks.branch_id')->get();
        
        $filename = "Laporan_Stok_" . date('Ymd_His') . ".csv";
        $handle = fopen('php://output', 'w');
        
        fputs($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        fputcsv($handle, ['Cabang', 'Barcode', 'Nama Produk', 'Kategori', 'Satuan', 'Min Stok', 'Stok Aktual', 'Harga Modal', 'Valuasi Aset', 'Status']);
        
        foreach ($stocks as $s) {
            $status = 'Aman';
            if($s->quantity == 0) $status = 'Habis';
            elseif($s->quantity <= $s->product->minimum_stock) $status = 'Menipis';
            
            $aset = $s->quantity * $s->product->purchase_price;

            fputcsv($handle, [
                $s->branch->name ?? '-',
                $s->product->barcode ?? '-',
                $s->product->name ?? '-',
                $s->product->category->name ?? '-',
                $s->product->unit->name ?? '-',
                $s->product->minimum_stock,
                $s->quantity,
                $s->product->purchase_price,
                $aset,
                $status
            ]);
        }
        
        fclose($handle);
        exit;
    }

    public function shifts(Request $request)
    {
        $shifts = \App\Models\Shift::with(['user', 'branch'])
            ->latest('start_time')
            ->paginate(15);
            
        return view('owner.shifts.index', compact('shifts'));
    }
}

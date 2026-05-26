<?php

namespace App\Http\Controllers\KepalaCabang;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockOut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockOutController extends Controller
{
    public function index(Request $request) {
        $query = StockOut::with(['product', 'creator'])
            ->where('branch_id', Auth::user()->branch_id);
            
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->whereHas('product', function($q2) use ($request) {
                    $q2->where('name', 'like', '%' . $request->search . '%');
                })->orWhere('notes', 'like', '%' . $request->search . '%');
            });
        }
        
        $stockOuts = $query->latest()->paginate(10);
        return view('kepala-cabang.stock-outs.index', compact('stockOuts'));
    }

    public function create() {
        $branchId = Auth::user()->branch_id;
        $products = Product::whereHas('stocks', fn($q) => $q->where('branch_id', $branchId)->where('quantity', '>', 0))->orderBy('name')->get();
        $stocks = Stock::where('branch_id', $branchId)->pluck('quantity', 'product_id');
        return view('kepala-cabang.stock-outs.create', compact('products', 'stocks'));
    }

    public function store(Request $request) {
        $request->validate([
            'date' => 'required|date',
            'reason' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $branchId = Auth::user()->branch_id;

        // Validasi stok dulu
        foreach ($request->items as $item) {
            $stock = Stock::where('branch_id', $branchId)->where('product_id', $item['product_id'])->first();
            if (!$stock || $stock->quantity < $item['quantity']) {
                return back()->withInput()->withErrors(['items' => 'Stok tidak mencukupi untuk salah satu produk yang dipilih.']);
            }
        }

        DB::transaction(function () use ($request, $branchId) {
            foreach ($request->items as $item) {
                StockOut::create([
                    'branch_id' => $branchId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'date' => $request->date,
                    'reason' => $request->reason,
                    'created_by' => Auth::id(),
                ]);
                $stock = Stock::where('branch_id', $branchId)->where('product_id', $item['product_id'])->first();
                $stock->decrement('quantity', $item['quantity']);
            }
        });

        return redirect()->route('kepala-cabang.stock-outs.index')->with('success', 'Batch Stok keluar berhasil dicatat.');
    }

    public function edit(StockOut $stock_out) {
        if ($stock_out->branch_id !== Auth::user()->branch_id) abort(403);
        return view('kepala-cabang.stock-outs.edit', compact('stock_out'));
    }

    public function update(Request $request, StockOut $stock_out) {
        if ($stock_out->branch_id !== Auth::user()->branch_id) abort(403);
        $request->validate(['reason' => 'nullable|string']);
        $stock_out->update($request->only('reason'));
        return redirect()->route('kepala-cabang.stock-outs.index')->with('success', 'Catatan stok keluar berhasil diperbarui.');
    }
}

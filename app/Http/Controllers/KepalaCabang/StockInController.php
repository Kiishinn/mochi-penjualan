<?php

namespace App\Http\Controllers\KepalaCabang;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockIn;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockInController extends Controller
{
    public function index(Request $request) {
        $query = StockIn::with(['product', 'supplier', 'creator'])
            ->where('branch_id', Auth::user()->branch_id);
            
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->whereHas('product', function($q2) use ($request) {
                    $q2->where('name', 'like', '%' . $request->search . '%');
                })->orWhere('notes', 'like', '%' . $request->search . '%');
            });
        }
            
        $stockIns = $query->latest()->paginate(10);
        return view('kepala-cabang.stock-ins.index', compact('stockIns'));
    }

    public function create() {
        $products = Product::orderBy('name')->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        return view('kepala-cabang.stock-ins.create', compact('products', 'suppliers'));
    }

    public function store(Request $request) {
        $request->validate([
            'date' => 'required|date',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'note' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $branchId = Auth::user()->branch_id;
            
            foreach ($request->items as $item) {
                StockIn::create([
                    'branch_id' => $branchId,
                    'product_id' => $item['product_id'],
                    'supplier_id' => $request->supplier_id,
                    'quantity' => $item['quantity'],
                    'date' => $request->date,
                    'note' => $request->note,
                    'created_by' => Auth::id(),
                ]);
                
                // Update stock
                $stock = Stock::firstOrCreate(['branch_id' => $branchId, 'product_id' => $item['product_id']], ['quantity' => 0]);
                $stock->increment('quantity', $item['quantity']);
            }
        });

        return redirect()->route('kepala-cabang.stock-ins.index')->with('success', 'Batch Stok masuk berhasil dicatat.');
    }

    public function edit(StockIn $stock_in) {
        if ($stock_in->branch_id !== Auth::user()->branch_id) abort(403);
        $products = Product::orderBy('name')->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        return view('kepala-cabang.stock-ins.edit', compact('stock_in', 'products', 'suppliers'));
    }

    public function update(Request $request, StockIn $stock_in) {
        if ($stock_in->branch_id !== Auth::user()->branch_id) abort(403);
        $request->validate(['note' => 'nullable|string']);
        $stock_in->update($request->only('note'));
        return redirect()->route('kepala-cabang.stock-ins.index')->with('success', 'Catatan stok masuk berhasil diperbarui.');
    }
}

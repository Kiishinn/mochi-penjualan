<?php

namespace App\Http\Controllers\KepalaCabang;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockTransferController extends Controller
{
    public function index() {
        $branchId = Auth::user()->branch_id;
        $transfers = StockTransfer::with(['fromBranch', 'toBranch', 'product', 'creator'])
            ->where('from_branch_id', $branchId)->orWhere('to_branch_id', $branchId)
            ->latest()->paginate(10);
        return view('kepala-cabang.stock-transfers.index', compact('transfers'));
    }

    public function create() {
        $branchId = Auth::user()->branch_id;
        $products = Product::whereHas('stocks', fn($q) => $q->where('branch_id', $branchId)->where('quantity', '>', 0))->orderBy('name')->get();
        $branches = Branch::where('id', '!=', $branchId)->get();
        $stocks = Stock::where('branch_id', $branchId)->pluck('quantity', 'product_id');
        return view('kepala-cabang.stock-transfers.create', compact('products', 'branches', 'stocks'));
    }

    public function store(Request $request) {
        $request->validate([
            'to_branch_id' => 'required|exists:branches,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'date' => 'required|date',
            'note' => 'nullable|string',
        ]);

        $branchId = Auth::user()->branch_id;
        $stock = Stock::where('branch_id', $branchId)->where('product_id', $request->product_id)->first();

        if (!$stock || $stock->quantity < $request->quantity) {
            return back()->withInput()->withErrors(['quantity' => 'Stok cabang asal tidak mencukupi.']);
        }

        StockTransfer::create([
            'from_branch_id' => $branchId,
            'to_branch_id' => $request->to_branch_id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'date' => $request->date,
            'status' => 'pending',
            'note' => $request->note,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('kepala-cabang.stock-transfers.index')->with('success', 'Perpindahan barang berhasil dicatat. Menunggu persetujuan.');
    }

    public function edit(StockTransfer $stock_transfer) {
        $branchId = Auth::user()->branch_id;
        if ($stock_transfer->from_branch_id !== $branchId && $stock_transfer->to_branch_id !== $branchId) abort(403);
        $stock_transfer->load('fromBranch', 'toBranch', 'product');
        return view('kepala-cabang.stock-transfers.edit', compact('stock_transfer'));
    }

    public function update(Request $request, StockTransfer $stock_transfer) {
        $branchId = Auth::user()->branch_id;
        if ($stock_transfer->from_branch_id !== $branchId && $stock_transfer->to_branch_id !== $branchId) abort(403);

        // Destination branch can approve/reject
        if ($request->has('status') && $stock_transfer->to_branch_id === $branchId && $stock_transfer->status === 'pending') {
            $request->validate(['status' => 'required|in:approved,rejected']);

            if ($request->status === 'approved') {
                $fromStock = Stock::where('branch_id', $stock_transfer->from_branch_id)
                                  ->where('product_id', $stock_transfer->product_id)->first();
                                  
                if (!$fromStock || $fromStock->quantity < $stock_transfer->quantity) {
                    return redirect()->back()->with('error', 'Gagal Setuju! Stok cabang asal ('.$stock_transfer->fromBranch->name.') saat ini tidak mencukupi.');
                }

                DB::transaction(function () use ($stock_transfer, $fromStock) {
                    // Kurangi stok cabang asal
                    $fromStock->decrement('quantity', $stock_transfer->quantity);

                    // Tambah stok cabang tujuan
                    $toStock = Stock::firstOrCreate(['branch_id' => $stock_transfer->to_branch_id, 'product_id' => $stock_transfer->product_id], ['quantity' => 0]);
                    $toStock->increment('quantity', $stock_transfer->quantity);

                    $stock_transfer->update(['status' => 'approved']);
                });
            } else {
                $stock_transfer->update(['status' => 'rejected']);
            }
            return redirect()->route('kepala-cabang.stock-transfers.index')->with('success', 'Status perpindahan barang berhasil diperbarui.');
        }

        // Source branch can update notes
        if ($stock_transfer->from_branch_id === $branchId) {
            $request->validate(['note' => 'nullable|string']);
            $stock_transfer->update($request->only('note'));
            return redirect()->route('kepala-cabang.stock-transfers.index')->with('success', 'Catatan berhasil diperbarui.');
        }

        return redirect()->route('kepala-cabang.stock-transfers.index');
    }
}

<?php

namespace App\Http\Controllers\KepalaCabang;

use App\Http\Controllers\Controller;
use App\Models\ReturnItem;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    public function index() {
        $returnItems = ReturnItem::with(['sale', 'product', 'user'])
            ->where('branch_id', Auth::user()->branch_id)->latest()->paginate(10);
        return view('kepala-cabang.returns.index', compact('returnItems'));
    }

    public function create() {
        $branchId = Auth::user()->branch_id;
        $sales = Sale::with('details.product')->where('branch_id', $branchId)->latest()->get();
        return view('kepala-cabang.returns.create', compact('sales'));
    }

    public function store(Request $request) {
        $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'reason' => 'nullable|string',
        ]);

        $branchId = Auth::user()->branch_id;
        $sale = Sale::where('branch_id', $branchId)->findOrFail($request->sale_id);

        // Validasi qty retur vs qty beli
        $saleDetail = SaleDetail::where('sale_id', $sale->id)->where('product_id', $request->product_id)->first();
        if (!$saleDetail) {
            return back()->withInput()->withErrors(['product_id' => 'Produk tidak ditemukan dalam transaksi ini.']);
        }
        $alreadyReturned = ReturnItem::where('sale_id', $sale->id)->where('product_id', $request->product_id)->sum('quantity');
        if ($request->quantity > ($saleDetail->quantity - $alreadyReturned)) {
            return back()->withInput()->withErrors(['quantity' => 'Jumlah retur melebihi jumlah pembelian.']);
        }

        ReturnItem::create([
            'sale_id' => $request->sale_id,
            'product_id' => $request->product_id,
            'branch_id' => $branchId,
            'user_id' => Auth::id(),
            'quantity' => $request->quantity,
            'reason' => $request->reason,
            'return_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        return redirect()->route('kepala-cabang.returns.index')->with('success', 'Pengajuan retur berhasil dicatat.');
    }

    public function show(ReturnItem $return) {
        if ($return->branch_id !== Auth::user()->branch_id) abort(403);
        $return->load('sale.details.product', 'product', 'user');
        return view('kepala-cabang.returns.show', compact('return'));
    }

    public function update(Request $request, ReturnItem $return) {
        if ($return->branch_id !== Auth::user()->branch_id) abort(403);
        if ($return->status !== 'pending') {
            return back()->withErrors(['error' => 'Retur sudah diproses.']);
        }

        $request->validate(['status' => 'required|in:approved']);

        DB::transaction(function () use ($return) {
            $return->update(['status' => 'approved']);
            // Kembalikan stok
            $stock = Stock::firstOrCreate(['branch_id' => $return->branch_id, 'product_id' => $return->product_id], ['quantity' => 0]);
            $stock->increment('quantity', $return->quantity);

            // Kurangi dari penjualan agar laporan akurat
            $saleDetail = SaleDetail::where('sale_id', $return->sale_id)->where('product_id', $return->product_id)->first();
            if ($saleDetail) {
                $deductQty = $return->quantity;
                $deductPrice = $deductQty * $saleDetail->price;

                $saleDetail->quantity -= $deductQty;
                $saleDetail->subtotal -= $deductPrice;
                $saleDetail->save();

                $sale = Sale::find($return->sale_id);
                if ($sale) {
                    $sale->total_price -= $deductPrice;
                    $sale->save();
                }
            }
        });

        return redirect()->route('kepala-cabang.returns.index')->with('success', 'Retur berhasil disetujui dan stok dikembalikan.');
    }
}

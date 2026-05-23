<?php

namespace App\Http\Controllers\Kasir;

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
        return view('kasir.returns.index', compact('returnItems'));
    }

    public function create() {
        $branchId = Auth::user()->branch_id;
        $sales = Sale::with('details.product')->where('branch_id', $branchId)->latest()->get();
        return view('kasir.returns.create', compact('sales'));
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

        return redirect()->route('kasir.returns.index')->with('success', 'Pengajuan retur berhasil dicatat.');
    }
}

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
    public function index(Request $request) {
        $query = ReturnItem::with(['sale', 'product', 'user'])
            ->where('branch_id', Auth::user()->branch_id);
            
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('reason', 'like', '%' . $request->search . '%')
                  ->orWhereHas('sale', function($q2) use ($request) {
                      $q2->where('receipt_number', 'like', '%' . $request->search . '%');
                  });
            });
        }
            
        $returnItems = $query->latest()->paginate(10);
        return view('kepala-cabang.returns.index', compact('returnItems'));
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

        $request->validate(['status' => 'required|in:approved,rejected']);

        if ($request->status === 'rejected') {
            $return->update(['status' => 'rejected']);
            return redirect()->route('kepala-cabang.returns.index')->with('success', 'Pengajuan retur telah ditolak.');
        }

        DB::transaction(function () use ($return) {
            $return->update(['status' => 'approved']);
            
            $stock = Stock::firstOrCreate(['branch_id' => $return->branch_id, 'product_id' => $return->product_id], ['quantity' => 0]);

            // Jika pelanggan minta tukar barang, kita berikan barang baru, jadi stok fisik di toko BERKURANG
            if ($return->return_type === 'exchange') {
                $stock->decrement('quantity', $return->quantity);
            }
            // Jika pelanggan minta refund, kita kembalikan uangnya
            else if ($return->return_type === 'refund') {
                // Tidak perlu mengubah Sale/SaleDetail agar riwayat transaksi tetap utuh
                // Refund akan mengurangi kas/pendapatan di laporan secara terpisah
            }

            // Jika barang yang diretur masih bagus, kita masukkan kembali ke stok gudang
            if ($return->item_condition === 'good') {
                $stock->increment('quantity', $return->quantity);
            }
        });

        return redirect()->route('kepala-cabang.returns.index')->with('success', 'Retur berhasil disetujui dan stok telah disesuaikan.');
    }
}

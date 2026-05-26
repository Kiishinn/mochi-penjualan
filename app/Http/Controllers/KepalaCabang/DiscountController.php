<?php

namespace App\Http\Controllers\KepalaCabang;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiscountController extends Controller
{
    public function index()
    {
        $branchId = Auth::user()->branch_id;
        $discounts = Discount::with('product')
            ->where('branch_id', $branchId)
            ->latest()
            ->paginate(10);
            
        return view('kepala-cabang.discounts.index', compact('discounts'));
    }

    public function create()
    {
        $products = Product::orderBy('name')->get();
        return view('kepala-cabang.discounts.create', compact('products'));
    }

    public function store(Request $request)
    {
        $rules = [
            'product_id' => 'required|exists:products,id',
            'discount_type' => 'required|in:percentage,nominal',
            'discount_value' => 'required|numeric|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ];

        // Validasi tambahan: persentase tidak boleh > 100
        if ($request->discount_type === 'percentage') {
            $rules['discount_value'] .= '|max:100';
        }

        // Validasi tambahan: nominal tidak boleh > harga jual produk
        if ($request->discount_type === 'nominal' && $request->product_id) {
            $product = Product::find($request->product_id);
            if ($product && $request->discount_value > $product->selling_price) {
                return back()->with('error', 'Nilai potongan tidak boleh melebihi harga jual produk (Rp ' . number_format($product->selling_price, 0, ',', '.') . ').')->withInput();
            }
        }

        $request->validate($rules);

        $branchId = Auth::user()->branch_id;

        // Cek apakah ada diskon yang masih pending atau aktif untuk produk ini di waktu yang beririsan
        $existing = Discount::where('branch_id', $branchId)
            ->where('product_id', $request->product_id)
            ->whereIn('status', ['pending', 'approved'])
            ->where(function($q) use ($request) {
                $q->whereBetween('start_date', [$request->start_date, $request->end_date])
                  ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                  ->orWhere(function($q2) use ($request) {
                      $q2->where('start_date', '<=', $request->start_date)
                         ->where('end_date', '>=', $request->end_date);
                  });
            })
            ->first();

        if ($existing) {
            return back()->with('error', 'Produk ini sudah memiliki pengajuan diskon aktif atau pending pada rentang tanggal tersebut.')->withInput();
        }

        Discount::create([
            'branch_id' => $branchId,
            'product_id' => $request->product_id,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => 'pending',
        ]);

        return redirect()->route('kepala-cabang.discounts.index')->with('success', 'Pengajuan diskon berhasil dibuat dan menunggu persetujuan Owner.');
    }

    public function destroy(Discount $discount)
    {
        if ($discount->branch_id !== Auth::user()->branch_id) abort(403);
        
        if ($discount->status !== 'pending') {
            return back()->with('error', 'Hanya pengajuan yang masih pending yang dapat dibatalkan.');
        }

        $discount->delete();
        return back()->with('success', 'Pengajuan diskon berhasil dibatalkan.');
    }
}

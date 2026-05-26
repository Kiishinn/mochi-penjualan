<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountApprovalController extends Controller
{
    public function index(Request $request)
    {
        $query = Discount::with(['product', 'branch']);
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'pending');
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $discounts = $query->latest()->paginate(10);
        $branches = \App\Models\Branch::orderBy('name')->get();

        return view('owner.discounts.index', compact('discounts', 'branches'));
    }

    public function approve(Discount $discount)
    {
        if ($discount->status !== 'pending') {
            return back()->with('error', 'Status diskon sudah tidak pending.');
        }

        $discount->update(['status' => 'approved']);
        return back()->with('success', 'Diskon berhasil disetujui.');
    }

    public function reject(Discount $discount)
    {
        if ($discount->status !== 'pending' && $discount->status !== 'approved') {
            return back()->with('error', 'Status diskon tidak valid untuk ditolak/diberhentikan.');
        }

        $discount->update(['status' => 'rejected']);
        return back()->with('success', 'Diskon berhasil ditolak / diberhentikan.');
    }
}

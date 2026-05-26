<?php

namespace App\Http\Controllers\KepalaCabang;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request) {
        // Also fetch stock_ins count
        $query = Supplier::withCount('stockIns');
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('contact_person', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
        }
        $suppliers = $query->orderBy('name')->paginate(10);
        return view('kepala-cabang.suppliers.index', compact('suppliers'));
    }
    public function create() { return view('kepala-cabang.suppliers.create'); }
    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'bank_account' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);
        
        $data = $request->only('name', 'contact_person', 'email', 'phone', 'address', 'bank_account');
        $data['is_active'] = $request->has('is_active');
        
        Supplier::create($data);
        return redirect()->route('kepala-cabang.suppliers.index')->with('success', 'Supplier berhasil ditambahkan.');
    }
    public function edit(Supplier $supplier) { return view('kepala-cabang.suppliers.edit', compact('supplier')); }
    public function update(Request $request, Supplier $supplier) {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'bank_account' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);
        
        $data = $request->only('name', 'contact_person', 'email', 'phone', 'address', 'bank_account');
        $data['is_active'] = $request->has('is_active');
        
        $supplier->update($data);
        return redirect()->route('kepala-cabang.suppliers.index')->with('success', 'Supplier berhasil diperbarui.');
    }
}

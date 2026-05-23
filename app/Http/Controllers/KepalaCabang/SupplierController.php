<?php

namespace App\Http\Controllers\KepalaCabang;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index() {
        return view('kepala-cabang.suppliers.index', ['suppliers' => Supplier::orderBy('name')->paginate(10)]);
    }
    public function create() { return view('kepala-cabang.suppliers.create'); }
    public function store(Request $request) {
        $request->validate(['name' => 'required|string|max:255', 'phone' => 'nullable|string|max:50', 'address' => 'nullable|string']);
        Supplier::create($request->only('name', 'phone', 'address'));
        return redirect()->route('kepala-cabang.suppliers.index')->with('success', 'Supplier berhasil ditambahkan.');
    }
    public function edit(Supplier $supplier) { return view('kepala-cabang.suppliers.edit', compact('supplier')); }
    public function update(Request $request, Supplier $supplier) {
        $request->validate(['name' => 'required|string|max:255', 'phone' => 'nullable|string|max:50', 'address' => 'nullable|string']);
        $supplier->update($request->only('name', 'phone', 'address'));
        return redirect()->route('kepala-cabang.suppliers.index')->with('success', 'Supplier berhasil diperbarui.');
    }
}

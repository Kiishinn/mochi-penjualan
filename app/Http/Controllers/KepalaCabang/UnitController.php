<?php

namespace App\Http\Controllers\KepalaCabang;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index(Request $request) {
        $query = Unit::query();
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('symbol', 'like', '%' . $request->search . '%');
        }
        return view('kepala-cabang.units.index', ['units' => $query->orderBy('name')->paginate(10)]);
    }
    public function create() { return view('kepala-cabang.units.create'); }
    public function store(Request $request) {
        $request->validate(['name' => 'required|string|max:255']);
        Unit::create($request->only('name'));
        return redirect()->route('kepala-cabang.units.index')->with('success', 'Satuan berhasil ditambahkan.');
    }
    public function edit(Unit $unit) { return view('kepala-cabang.units.edit', compact('unit')); }
    public function update(Request $request, Unit $unit) {
        $request->validate(['name' => 'required|string|max:255']);
        $unit->update($request->only('name'));
        return redirect()->route('kepala-cabang.units.index')->with('success', 'Satuan berhasil diperbarui.');
    }
}

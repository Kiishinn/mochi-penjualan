<?php

namespace App\Http\Controllers\KepalaCabang;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request) {
        $query = Category::query();
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        return view('kepala-cabang.categories.index', ['categories' => $query->orderBy('name')->paginate(10)]);
    }
    public function create() { return view('kepala-cabang.categories.create'); }
    public function store(Request $request) {
        $request->validate(['name' => 'required|string|max:255']);
        Category::create($request->only('name'));
        return redirect()->route('kepala-cabang.categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }
    public function edit(Category $category) { return view('kepala-cabang.categories.edit', compact('category')); }
    public function update(Request $request, Category $category) {
        $request->validate(['name' => 'required|string|max:255']);
        $category->update($request->only('name'));
        return redirect()->route('kepala-cabang.categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }
}

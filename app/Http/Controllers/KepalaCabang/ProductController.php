<?php

namespace App\Http\Controllers\KepalaCabang;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index() {
        $products = Product::with(['category', 'unit'])->orderBy('name')->paginate(10);
        // Attach stock quantity for this branch
        $branchId = Auth::user()->branch_id;
        $stocks = Stock::where('branch_id', $branchId)->pluck('quantity', 'product_id');
        return view('kepala-cabang.products.index', compact('products', 'stocks'));
    }
    public function create() {
        $categories = Category::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        return view('kepala-cabang.products.create', compact('categories', 'units'));
    }
    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'barcode' => 'nullable|string|unique:products,barcode',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'minimum_stock' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ]);
        $product = Product::create($request->only('name', 'category_id', 'unit_id', 'barcode', 'purchase_price', 'selling_price', 'minimum_stock', 'description'));
        // Create stock entry for all branches with 0
        $branches = \App\Models\Branch::all();
        foreach ($branches as $branch) {
            Stock::firstOrCreate(['branch_id' => $branch->id, 'product_id' => $product->id], ['quantity' => 0]);
        }
        return redirect()->route('kepala-cabang.products.index')->with('success', 'Produk berhasil ditambahkan.');
    }
    public function edit(Product $product) {
        $categories = Category::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        return view('kepala-cabang.products.edit', compact('product', 'categories', 'units'));
    }
    public function update(Request $request, Product $product) {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'barcode' => 'nullable|string|unique:products,barcode,' . $product->id,
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'minimum_stock' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ]);
        $product->update($request->only('name', 'category_id', 'unit_id', 'barcode', 'purchase_price', 'selling_price', 'minimum_stock', 'description'));
        return redirect()->route('kepala-cabang.products.index')->with('success', 'Produk berhasil diperbarui.');
    }
}

<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;

class MasterDataController extends Controller
{
    public function categories() { return view('owner.categories.index', ['categories' => Category::orderBy('name')->paginate(10)]); }
    public function units() { return view('owner.units.index', ['units' => Unit::orderBy('name')->paginate(10)]); }
    public function suppliers() { return view('owner.suppliers.index', ['suppliers' => Supplier::orderBy('name')->paginate(10)]); }
    public function products() {
        $products = Product::with(['category', 'unit'])->orderBy('name')->paginate(10);
        return view('owner.products.index', compact('products'));
    }
}

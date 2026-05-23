<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index() {
        $branchId = Auth::user()->branch_id;
        
        $currentShift = \App\Models\Shift::where('user_id', Auth::id())->where('status', 'open')->first();
        if (!$currentShift) {
            return redirect()->route('kasir.shifts.create')->with('error', 'Anda harus membuka shift terlebih dahulu sebelum mengakses menu transaksi.');
        }

        $sales = Sale::with(['user', 'details.product'])
            ->where('branch_id', $branchId)->latest()->paginate(10);
        return view('kasir.transactions.index', compact('sales'));
    }

    public function create() {
        $currentShift = \App\Models\Shift::where('user_id', Auth::id())->where('status', 'open')->first();
        if (!$currentShift) {
            return redirect()->route('kasir.shifts.create')->with('error', 'Anda harus membuka shift terlebih dahulu sebelum melakukan transaksi.');
        }

        $branchId = Auth::user()->branch_id;
        $stocks = Stock::with(['product.category', 'product.unit'])
            ->where('branch_id', $branchId)->where('quantity', '>', 0)->get();
        return view('kasir.transactions.create', compact('stocks'));
    }

    public function store(Request $request) {
        $request->validate([
            'cart' => 'required|string',
            'paid_amount' => 'required|numeric|min:0',
        ]);

        $cart = json_decode($request->cart, true);
        if (empty($cart)) {
            return back()->withErrors(['error' => 'Keranjang belanja kosong.']);
        }

        try {
            DB::beginTransaction();
            $branchId = Auth::user()->branch_id;
            
            $currentShift = \App\Models\Shift::where('user_id', Auth::id())->where('status', 'open')->first();
            if (!$currentShift) {
                throw new \Exception("Anda harus membuka shift terlebih dahulu.");
            }

            $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
            $totalPrice = 0;

            // Validate stock & calculate
            foreach ($cart as $item) {
                $stock = Stock::where('branch_id', $branchId)->where('product_id', $item['product_id'])->lockForUpdate()->first();
                if (!$stock || $stock->quantity < $item['qty']) {
                    $product = Product::find($item['product_id']);
                    throw new \Exception("Stok {$product->name} tidak mencukupi.");
                }
                $totalPrice += $item['price'] * $item['qty'];
            }

            $paidAmount = $request->paid_amount;
            $changeAmount = $paidAmount - $totalPrice;

            if ($changeAmount < 0) {
                throw new \Exception('Uang bayar kurang dari total belanja.');
            }

            $sale = Sale::create([
                'invoice_number' => $invoiceNumber,
                'branch_id' => $branchId,
                'user_id' => Auth::id(),
                'shift_id' => $currentShift->id,
                'total_price' => $totalPrice,
                'paid_amount' => $paidAmount,
                'change_amount' => $changeAmount,
                'transaction_date' => now(),
            ]);

            foreach ($cart as $item) {
                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => $item['price'] * $item['qty'],
                ]);
                // Kurangi stok
                Stock::where('branch_id', $branchId)->where('product_id', $item['product_id'])->decrement('quantity', $item['qty']);
            }

            DB::commit();
            return redirect()->route('kasir.transactions.show', $sale->id)->with('success', 'Transaksi berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show(Sale $transaction) {
        if ($transaction->branch_id !== Auth::user()->branch_id) abort(403);
        $transaction->load('details.product', 'user', 'branch');
        return view('kasir.transactions.show', compact('transaction'));
    }
}

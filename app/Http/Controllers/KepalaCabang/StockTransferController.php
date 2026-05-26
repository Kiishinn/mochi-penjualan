<?php

namespace App\Http\Controllers\KepalaCabang;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockTransferController extends Controller
{
    /**
     * Tampilkan daftar semua transfer (keluar & masuk) untuk cabang ini.
     */
    public function index(Request $request) {
        $branchId = Auth::user()->branch_id;
        $tab = $request->get('tab', 'outgoing'); // outgoing = permintaan saya, incoming = diminta dari saya

        $query = StockTransfer::with(['fromBranch', 'toBranch', 'product', 'creator']);

        if ($tab === 'incoming') {
            // Permintaan MASUK: cabang saya = sumber (from_branch_id)
            $query->where('from_branch_id', $branchId);
        } else {
            // Permintaan KELUAR: cabang saya = peminta (to_branch_id)
            $query->where('to_branch_id', $branchId);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->whereHas('product', function($q2) use ($request) {
                    $q2->where('name', 'like', '%' . $request->search . '%');
                });
            });
        }
            
        $transfers = $query->latest()->paginate(10)->appends($request->query());

        // Hitung badge counts
        $pendingIncoming = StockTransfer::where('from_branch_id', $branchId)->where('status', 'pending')->count();
        $approvedOutgoing = StockTransfer::where('to_branch_id', $branchId)->where('status', 'approved')->count();

        return view('kepala-cabang.stock-transfers.index', compact('transfers', 'tab', 'pendingIncoming', 'approvedOutgoing'));
    }

    /**
     * Form permintaan transfer: tampilkan stok cabang lain.
     */
    public function create(Request $request) {
        $branchId = Auth::user()->branch_id;
        $branches = Branch::where('id', '!=', $branchId)->orderBy('name')->get();

        // Ambil stok semua cabang lain untuk referensi
        $allStocks = collect();
        $selectedBranch = null;

        if ($request->filled('source_branch_id')) {
            $selectedBranch = $request->source_branch_id;
            $allStocks = Stock::with(['product.category', 'product.unit'])
                ->where('branch_id', $selectedBranch)
                ->where('quantity', '>', 0)
                ->get();
        }

        return view('kepala-cabang.stock-transfers.create', compact('branches', 'allStocks', 'selectedBranch'));
    }

    /**
     * Simpan permintaan transfer (status: pending).
     * Stok BELUM berubah.
     */
    public function store(Request $request) {
        $request->validate([
            'from_branch_id' => 'required|exists:branches,id',
            'date' => 'required|date',
            'requester_note' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_requested' => 'required|integer|min:1',
        ]);

        $branchId = Auth::user()->branch_id;

        if ($request->from_branch_id == $branchId) {
            return back()->withInput()->with('error', 'Tidak bisa meminta stok dari cabang sendiri.');
        }

        DB::transaction(function () use ($request, $branchId) {
            foreach ($request->items as $item) {
                StockTransfer::create([
                    'from_branch_id' => $request->from_branch_id,
                    'to_branch_id' => $branchId,
                    'product_id' => $item['product_id'],
                    'quantity_requested' => $item['quantity_requested'],
                    'date' => $request->date,
                    'status' => 'pending',
                    'requester_note' => $request->requester_note,
                    'created_by' => Auth::id(),
                ]);
            }
        });

        return redirect()->route('kepala-cabang.stock-transfers.index')->with('success', 'Permintaan transfer berhasil dikirim. Menunggu persetujuan cabang sumber.');
    }

    /**
     * Detail transfer
     */
    public function edit(StockTransfer $stock_transfer) {
        $branchId = Auth::user()->branch_id;
        if ($stock_transfer->from_branch_id !== $branchId && $stock_transfer->to_branch_id !== $branchId) {
            abort(403);
        }
        $stock_transfer->load('fromBranch', 'toBranch', 'product', 'creator');

        // Jika cabang sumber sedang mau approve, tampilkan stok saat ini
        $currentStock = null;
        if ($stock_transfer->from_branch_id === $branchId) {
            $currentStock = Stock::where('branch_id', $branchId)
                ->where('product_id', $stock_transfer->product_id)
                ->first();
        }

        return view('kepala-cabang.stock-transfers.edit', compact('stock_transfer', 'currentStock'));
    }

    /**
     * Cabang SUMBER menyetujui permintaan.
     * Stok sumber BERKURANG sebanyak quantity_sent.
     */
    public function approve(StockTransfer $stock_transfer, Request $request) {
        $branchId = Auth::user()->branch_id;

        if ($stock_transfer->from_branch_id !== $branchId) abort(403);
        if ($stock_transfer->status !== 'pending') {
            return back()->with('error', 'Permintaan ini sudah tidak berstatus pending.');
        }

        $request->validate([
            'quantity_sent' => 'required|integer|min:1|max:' . $stock_transfer->quantity_requested,
            'source_note' => 'nullable|string',
        ]);

        $stock = Stock::where('branch_id', $branchId)
            ->where('product_id', $stock_transfer->product_id)
            ->first();

        if (!$stock || $stock->quantity < $request->quantity_sent) {
            return back()->with('error', 'Stok cabang Anda tidak mencukupi untuk mengirim jumlah tersebut.');
        }

        DB::transaction(function () use ($stock_transfer, $stock, $request) {
            // Kurangi stok cabang sumber
            $stock->decrement('quantity', $request->quantity_sent);

            $stock_transfer->update([
                'status' => 'approved',
                'quantity_sent' => $request->quantity_sent,
                'source_note' => $request->source_note,
            ]);
        });

        return redirect()->route('kepala-cabang.stock-transfers.index', ['tab' => 'incoming'])
            ->with('success', 'Permintaan disetujui. Stok Anda telah berkurang sebanyak ' . $request->quantity_sent . ' unit.');
    }

    /**
     * Cabang SUMBER menolak permintaan.
     * Stok TIDAK berubah.
     */
    public function reject(StockTransfer $stock_transfer, Request $request) {
        $branchId = Auth::user()->branch_id;

        if ($stock_transfer->from_branch_id !== $branchId) abort(403);
        if ($stock_transfer->status !== 'pending') {
            return back()->with('error', 'Permintaan ini sudah tidak berstatus pending.');
        }

        $request->validate([
            'source_note' => 'nullable|string',
        ]);

        $stock_transfer->update([
            'status' => 'rejected',
            'source_note' => $request->source_note,
        ]);

        return redirect()->route('kepala-cabang.stock-transfers.index', ['tab' => 'incoming'])
            ->with('success', 'Permintaan transfer berhasil ditolak.');
    }

    /**
     * Cabang PEMINTA mengkonfirmasi barang sudah diterima.
     * Stok peminta BERTAMBAH sebanyak quantity_sent.
     */
    public function receive(StockTransfer $stock_transfer) {
        $branchId = Auth::user()->branch_id;

        if ($stock_transfer->to_branch_id !== $branchId) abort(403);
        if ($stock_transfer->status !== 'approved') {
            return back()->with('error', 'Barang belum disetujui/dikirim oleh cabang sumber.');
        }

        DB::transaction(function () use ($stock_transfer) {
            // Tambah stok cabang peminta
            $toStock = Stock::firstOrCreate(
                ['branch_id' => $stock_transfer->to_branch_id, 'product_id' => $stock_transfer->product_id],
                ['quantity' => 0]
            );
            $toStock->increment('quantity', $stock_transfer->quantity_sent);

            $stock_transfer->update(['status' => 'received']);
        });

        return redirect()->route('kepala-cabang.stock-transfers.index')
            ->with('success', 'Barang berhasil dikonfirmasi diterima. Stok Anda bertambah ' . $stock_transfer->quantity_sent . ' unit.');
    }
}

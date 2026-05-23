@extends('layouts.dashboard')

@section('title', 'Detail Mutasi Barang')
@section('page-title', 'Persetujuan Mutasi')

@section('content')
    <div class="card" style="max-width: 800px;">
        <div class="card-header">
            <h3>Mutasi #{{ $stock_transfer->id }}</h3>
            <a href="{{ route('kepala-cabang.stock-transfers.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
        </div>

        <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:1.5rem; margin-bottom:2rem; padding-bottom:1.5rem; border-bottom:1px solid var(--border-color);">
            <div>
                <p style="font-size:0.8125rem; color:var(--text-muted); margin-bottom:0.25rem;">Tanggal Pengajuan</p>
                <p style="font-weight:500;">{{ \Carbon\Carbon::parse($stock_transfer->date)->format('d F Y') }}</p>
            </div>
            <div>
                <p style="font-size:0.8125rem; color:var(--text-muted); margin-bottom:0.25rem;">Cabang Asal</p>
                <p style="font-weight:500;">{{ $stock_transfer->fromBranch->name ?? '-' }}</p>
            </div>
            <div>
                <p style="font-size:0.8125rem; color:var(--text-muted); margin-bottom:0.25rem;">Cabang Tujuan</p>
                <p style="font-weight:500;">{{ $stock_transfer->toBranch->name ?? '-' }}</p>
            </div>
            <div>
                <p style="font-size:0.8125rem; color:var(--text-muted); margin-bottom:0.25rem;">Status Mutasi</p>
                @if($stock_transfer->status === 'pending')
                    <span class="badge" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">Menunggu Persetujuan</span>
                @elseif($stock_transfer->status === 'approved')
                    <span class="badge" style="background: rgba(34, 197, 94, 0.1); color: #22c55e;">Disetujui & Diterima</span>
                @else
                    <span class="badge" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">Ditolak</span>
                @endif
            </div>
        </div>

        <h4 style="margin-bottom:1rem;">Informasi Barang</h4>
        <div style="background: var(--bg-input); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3 style="margin: 0 0 0.5rem 0; color: var(--text-primary);">{{ $stock_transfer->product->name }}</h3>
                    <p style="margin: 0; color: var(--text-muted);">Barcode: {{ $stock_transfer->product->barcode ?? '-' }}</p>
                </div>
                <div style="text-align: right;">
                    <p style="margin: 0; font-size: 0.85rem; color: var(--text-muted);">Jumlah Mutasi</p>
                    <p style="margin: 0; font-size: 1.5rem; font-weight: 700; color: var(--accent);">{{ $stock_transfer->quantity }}</p>
                </div>
            </div>
        </div>

        @if($stock_transfer->to_branch_id == Auth::user()->branch_id && $stock_transfer->status === 'pending')
            <div style="background: rgba(34, 197, 94, 0.05); border: 1px solid rgba(34, 197, 94, 0.2); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem;">
                <h4 style="margin-bottom: 1rem; color: #166534;">Konfirmasi Penerimaan Mutasi</h4>
                <p style="font-size: 0.875rem; color: #15803d; margin-bottom: 1.5rem;">
                    Cabang lain mengirimkan barang ini ke cabang Anda. Pastikan jumlah fisik barang yang Anda terima sesuai sebelum menekan tombol "Terima". Jika ada ketidaksesuaian atau rusak, Anda dapat menolaknya.
                </p>
                
                <form action="{{ route('kepala-cabang.stock-transfers.update', $stock_transfer->id) }}" method="POST" style="display: flex; gap: 1rem;">
                    @csrf
                    @method('PUT')
                    
                    <button type="submit" name="status" value="approved" class="btn btn-primary" style="background: var(--success); border-color: var(--success);" onclick="return confirm('Stok cabang Anda akan bertambah sejumlah {{ $stock_transfer->quantity }}. Lanjutkan?')">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                        Terima Barang
                    </button>
                    
                    <button type="submit" name="status" value="rejected" class="btn btn-secondary" style="color: var(--danger); border-color: var(--danger);" onclick="return confirm('Anda menolak mutasi ini. Stok tidak akan ditambahkan. Lanjutkan?')">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                        Tolak
                    </button>
                </form>
            </div>
        @elseif($stock_transfer->from_branch_id == Auth::user()->branch_id)
            <form action="{{ route('kepala-cabang.stock-transfers.update', $stock_transfer->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="note">Catatan Tambahan (Resi / Ekspedisi / Keterangan)</label>
                    <textarea name="note" id="note" class="form-control" rows="3">{{ old('note', $stock_transfer->note) }}</textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Simpan Catatan</button>
                </div>
            </form>
        @else
            <div class="form-group">
                <label>Catatan Mutasi</label>
                <div style="padding: 1rem; background: var(--bg-input); border-radius: 8px; color: var(--text-secondary); min-height: 3rem;">
                    {{ $stock_transfer->note ?: 'Tidak ada catatan.' }}
                </div>
            </div>
        @endif
    </div>
@endsection

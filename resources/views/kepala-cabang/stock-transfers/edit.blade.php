@extends('layouts.dashboard')

@section('title', 'Detail Permintaan Stok')
@section('page-title', 'Detail Permintaan Stok')

@section('content')
    <div class="card" style="max-width: 800px;">
        <div class="card-header">
            <h3>Detail Permintaan #{{ $stock_transfer->id }}</h3>
            <a href="{{ route('kepala-cabang.stock-transfers.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
        </div>

        <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:1.5rem; margin-bottom:2rem; padding-bottom:1.5rem; border-bottom:1px solid var(--border-color);">
            <div>
                <p style="font-size:0.8125rem; color:var(--text-muted); margin-bottom:0.25rem;">Tanggal Pengajuan</p>
                <p style="font-weight:500;">{{ \Carbon\Carbon::parse($stock_transfer->date)->format('d F Y H:i') }} WIB</p>
            </div>
            <div>
                <p style="font-size:0.8125rem; color:var(--text-muted); margin-bottom:0.25rem;">Cabang Peminta</p>
                <p style="font-weight:500;">{{ $stock_transfer->toBranch->name ?? '-' }}</p>
            </div>
            <div>
                <p style="font-size:0.8125rem; color:var(--text-muted); margin-bottom:0.25rem;">Cabang Sumber (Tujuan Permintaan)</p>
                <p style="font-weight:500;">{{ $stock_transfer->fromBranch->name ?? '-' }}</p>
            </div>
            <div>
                <p style="font-size:0.8125rem; color:var(--text-muted); margin-bottom:0.25rem;">Status Permintaan</p>
                @if($stock_transfer->status === 'pending')
                    <span class="badge badge-warning">Menunggu Persetujuan</span>
                @elseif($stock_transfer->status === 'approved')
                    <span class="badge badge-kasir">Disetujui / Sedang Dikirim</span>
                @elseif($stock_transfer->status === 'received')
                    <span class="badge badge-owner">Selesai / Diterima</span>
                @else
                    <span class="badge" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">Ditolak</span>
                @endif
            </div>
            <div>
                <p style="font-size:0.8125rem; color:var(--text-muted); margin-bottom:0.25rem;">Dibuat Oleh</p>
                <p style="font-weight:500;">{{ $stock_transfer->creator->name ?? '-' }}</p>
            </div>
        </div>

        <h4 style="margin-bottom:1rem;">Informasi Barang</h4>
        <div style="background: var(--bg-input); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3 style="margin: 0 0 0.5rem 0; color: var(--text-primary);">{{ $stock_transfer->product->name }}</h3>
                </div>
                <div style="text-align: right; display: flex; gap: 2rem;">
                    <div>
                        <p style="margin: 0; font-size: 0.85rem; color: var(--text-muted);">Jumlah Diminta</p>
                        <p style="margin: 0; font-size: 1.5rem; font-weight: 700; color: var(--accent);">{{ $stock_transfer->quantity_requested }}</p>
                    </div>
                    @if($stock_transfer->status !== 'pending')
                        <div>
                            <p style="margin: 0; font-size: 0.85rem; color: var(--text-muted);">Jumlah Dikirim</p>
                            <p style="margin: 0; font-size: 1.5rem; font-weight: 700; color: var(--success);">{{ $stock_transfer->quantity_sent ?? '-' }}</p>
                        </div>
                    @endif
                </div>
            </div>
            @if($currentStock && $stock_transfer->status === 'pending')
                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px dashed var(--border-color); color: var(--text-secondary); font-size: 0.875rem;">
                    Stok Anda Saat Ini: <strong style="color: var(--text-primary);">{{ $currentStock->quantity }}</strong> unit
                </div>
            @endif
        </div>

        <!-- Catatan -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
            <div>
                <p style="font-size:0.8125rem; color:var(--text-muted); margin-bottom:0.25rem;">Catatan dari Peminta</p>
                <div style="padding: 1rem; background: var(--bg-input); border-radius: 8px; color: var(--text-secondary); min-height: 4rem;">
                    {{ $stock_transfer->requester_note ?: 'Tidak ada catatan.' }}
                </div>
            </div>
            @if($stock_transfer->status !== 'pending' || $stock_transfer->source_note)
                <div>
                    <p style="font-size:0.8125rem; color:var(--text-muted); margin-bottom:0.25rem;">Catatan dari Sumber</p>
                    <div style="padding: 1rem; background: var(--bg-input); border-radius: 8px; color: var(--text-secondary); min-height: 4rem;">
                        {{ $stock_transfer->source_note ?: 'Tidak ada catatan.' }}
                    </div>
                </div>
            @endif
        </div>

        <!-- 1. Form Persetujuan (Cabang SUMBER) -->
        @if($stock_transfer->from_branch_id == Auth::user()->branch_id && $stock_transfer->status === 'pending')
            <div style="background: rgba(34, 197, 94, 0.05); border: 1px solid rgba(34, 197, 94, 0.2); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem;">
                <h4 style="margin-bottom: 1rem; color: #166534;">Tindak Lanjuti Permintaan</h4>
                <p style="font-size: 0.875rem; color: #15803d; margin-bottom: 1.5rem;">
                    Cabang peminta membutuhkan stok Anda. Anda dapat menentukan berapa jumlah yang benar-benar bisa Anda kirim (bisa kurang dari yang diminta).
                </p>
                
                <form id="approve-form" action="{{ route('kepala-cabang.stock-transfers.approve', $stock_transfer->id) }}" method="POST" style="margin-bottom: 1rem;">
                    @csrf
                    <div class="form-grid" style="margin-bottom: 1rem;">
                        <div class="form-group">
                            <label style="color: #166534;">Jumlah yang akan Dikirim <span style="color: #ef4444;">*</span></label>
                            <input type="number" name="quantity_sent" class="form-control" value="{{ $stock_transfer->quantity_requested }}" max="{{ min($stock_transfer->quantity_requested, $currentStock->quantity ?? 0) }}" min="1" required style="border-color: rgba(34, 197, 94, 0.3);">
                        </div>
                        <div class="form-group">
                            <label style="color: #166534;">Catatan untuk Peminta (Opsional)</label>
                            <input type="text" name="source_note" id="approve_source_note" class="form-control" placeholder="Contoh: Barang dikirim via kurir internal" style="border-color: rgba(34, 197, 94, 0.3);">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" style="background: var(--success); border-color: var(--success);" onclick="return confirm('Menyetujui permintaan ini akan MENGURANGI stok Anda sesuai jumlah yang dikirim. Lanjutkan?')">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                        Setujui & Kurangi Stok Saya
                    </button>
                </form>

                <hr style="border: 0; border-top: 1px dashed rgba(34, 197, 94, 0.2); margin: 1.5rem 0;">

                <form action="{{ route('kepala-cabang.stock-transfers.reject', $stock_transfer->id) }}" method="POST" style="display: flex; gap: 1rem; align-items: flex-end;">
                    @csrf
                    <div class="form-group" style="margin-bottom: 0; flex: 1;">
                        <label style="color: var(--danger);">Alasan Penolakan (Opsional)</label>
                        <input type="text" name="source_note" id="reject_source_note" class="form-control" placeholder="Contoh: Stok sedang menipis, tidak bisa kirim" style="border-color: rgba(239, 68, 68, 0.3);">
                    </div>
                    <button type="submit" class="btn btn-secondary" style="color: var(--danger); border-color: var(--danger); background: transparent;" onclick="return confirm('Tolak permintaan ini? Stok Anda tidak akan berubah.')">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                        Tolak Permintaan
                    </button>
                </form>
            </div>
            
            <script>
                // Sinkronisasi note input
                document.getElementById('approve_source_note').addEventListener('input', function(e) {
                    document.getElementById('reject_source_note').value = e.target.value;
                });
                document.getElementById('reject_source_note').addEventListener('input', function(e) {
                    document.getElementById('approve_source_note').value = e.target.value;
                });
            </script>
        @endif

        <!-- 2. Form Konfirmasi Terima (Cabang PEMINTA) -->
        @if($stock_transfer->to_branch_id == Auth::user()->branch_id && $stock_transfer->status === 'approved')
            <div style="background: rgba(34, 197, 94, 0.05); border: 1px solid rgba(34, 197, 94, 0.2); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem;">
                <h4 style="margin-bottom: 1rem; color: #166534;">Konfirmasi Penerimaan Barang</h4>
                <p style="font-size: 0.875rem; color: #15803d; margin-bottom: 1.5rem;">
                    Cabang sumber telah menyetujui dan mengirimkan <strong>{{ $stock_transfer->quantity_sent }}</strong> unit. Klik tombol di bawah ini JIKA BARANG SUDAH ANDA TERIMA secara fisik.
                </p>
                
                <form action="{{ route('kepala-cabang.stock-transfers.receive', $stock_transfer->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="background: var(--success); border-color: var(--success);" onclick="return confirm('Stok cabang Anda akan DITAMBAHKAN sejumlah {{ $stock_transfer->quantity_sent }} unit. Lanjutkan?')">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        Konfirmasi Barang Diterima
                    </button>
                </form>
            </div>
        @endif
    </div>
@endsection

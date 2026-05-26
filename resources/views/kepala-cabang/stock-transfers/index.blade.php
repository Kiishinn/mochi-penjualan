@extends('layouts.dashboard')

@section('title', 'Perpindahan Barang')
@section('page-title', 'Perpindahan Barang')

@section('content')
    <div class="card">
        <div class="card-header" style="flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <h3>Riwayat Perpindahan Barang Antar Cabang</h3>
                <a href="{{ route('kepala-cabang.stock-transfers.create') }}" class="btn btn-primary btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Buat Perpindahan
                </a>
            </div>
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center; margin-left: auto;">
                <form id="filterForm" method="GET" action="{{ route('kepala-cabang.stock-transfers.index') }}" style="display: flex; gap: 0.5rem;">
                    <input type="text" id="searchInput" name="search" class="form-control" placeholder="Cari produk/keterangan..." value="{{ request('search') }}" style="width: 250px; padding: 0.25rem 0.5rem;" autocomplete="off">
                    @if(request('search') != '')
                        <a href="{{ route('kepala-cabang.stock-transfers.index') }}" class="btn btn-secondary btn-sm" style="background: transparent; color: var(--danger); border-color: var(--danger);">Reset</a>
                    @endif
                </form>
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Tipe</th>
                        <th>Cabang Asal/Tujuan</th>
                        <th>Produk</th>
                        <th style="text-align: center;">Jumlah</th>
                        <th>Status</th>
                        <th style="width: 120px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $myBranchId = Auth::user()->branch_id; @endphp
                    @forelse($transfers as $transfer)
                        <tr>
                            <td>
                                <div>{{ \Carbon\Carbon::parse($transfer->date)->format('d M Y') }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">{{ \Carbon\Carbon::parse($transfer->date)->format('H:i') }} WIB</div>
                            </td>
                            <td>
                                @if($transfer->from_branch_id === $myBranchId)
                                    <span class="badge" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">Keluar</span>
                                @else
                                    <span class="badge badge-kasir">Masuk</span>
                                @endif
                            </td>
                            <td>
                                @if($transfer->from_branch_id === $myBranchId)
                                    Ke: {{ $transfer->toBranch->name ?? '-' }}
                                @else
                                    Dari: {{ $transfer->fromBranch->name ?? '-' }}
                                @endif
                            </td>
                            <td>{{ $transfer->product->name ?? '-' }}</td>
                            <td style="text-align: center;">{{ $transfer->quantity }}</td>
                            <td>
                                @if($transfer->status === 'pending')
                                    <span class="badge badge-warning">Menunggu</span>
                                @elseif($transfer->status === 'approved')
                                    <span class="badge badge-kasir">Disetujui</span>
                                @else
                                    <span class="badge" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">Ditolak</span>
                                @endif
                            </td>
                            <td style="text-align: center; display: flex; gap: 0.5rem; justify-content: center;">
                                <a href="{{ route('kepala-cabang.stock-transfers.edit', $transfer->id) }}" class="btn btn-sm" style="background: rgba(6, 182, 212, 0.1); color: var(--accent); padding: 4px 8px; text-decoration: none; border-radius: 4px;">Detail</a>
                                
                                @if($transfer->status === 'pending')
                                    @if($transfer->to_branch_id === $myBranchId)
                                        <form action="{{ route('kepala-cabang.stock-transfers.update', $transfer->id) }}" method="POST" style="display:inline;">
                                            @csrf @method('PUT')
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="btn btn-sm badge-kasir" style="border:none; cursor:pointer; padding: 4px 8px;" onclick="return confirm('Terima mutasi barang ini?')">Terima</button>
                                        </form>
                                        <form action="{{ route('kepala-cabang.stock-transfers.update', $transfer->id) }}" method="POST" style="display:inline;">
                                            @csrf @method('PUT')
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="btn btn-sm" style="border:none; cursor:pointer; padding: 4px 8px; background: #ef4444; color: #fff;" onclick="return confirm('Tolak mutasi barang ini?')">Tolak</button>
                                        </form>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem;">Belum ada riwayat perpindahan barang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $transfers->links() }}
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let searchTimeout;
            const searchInput = document.getElementById('searchInput');
            const filterForm = document.getElementById('filterForm');

            if(searchInput) {
                if (searchInput.value.length > 0) {
                    const length = searchInput.value.length;
                    if (window.innerWidth > 768) searchInput.focus();
                    searchInput.setSelectionRange(length, length);
                }

                            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    filterForm.submit();
                }, 800);
            });
            
                        window.addEventListener('beforeunload', function() {
                clearTimeout(searchTimeout);
            });
            document.body.addEventListener('click', function(e) {
                if (e.target.closest('a') || e.target.closest('button')) {
                    clearTimeout(searchTimeout);
                }
            });}
        });
    </script>
@endsection




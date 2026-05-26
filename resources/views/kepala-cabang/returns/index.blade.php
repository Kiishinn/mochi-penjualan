@extends('layouts.dashboard')

@section('title', 'Data Retur Barang')
@section('page-title', 'Data Retur Barang')

@section('content')
    <div class="card">
        <div class="card-header" style="flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <h3>Riwayat Pengajuan Retur Barang (Dari Pelanggan)</h3>
                <a href="{{ route('kepala-cabang.returns.create') }}" class="btn btn-primary btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Buat Pengajuan Retur
                </a>
            </div>
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center; margin-left: auto;">
                <form id="filterForm" method="GET" action="{{ route('kepala-cabang.returns.index') }}" style="display: flex; gap: 0.5rem;">
                    <input type="text" id="searchInput" name="search" class="form-control" placeholder="Cari no. kuitansi/alasan..." value="{{ request('search') }}" style="width: 250px; padding: 0.25rem 0.5rem;" autocomplete="off">
                    @if(request('search') != '')
                        <a href="{{ route('kepala-cabang.returns.index') }}" class="btn btn-secondary btn-sm" style="background: transparent; color: var(--danger); border-color: var(--danger);">Reset</a>
                    @endif
                </form>
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>No Invoice</th>
                        <th>Produk</th>
                        <th style="text-align: center;">Jumlah</th>
                        <th>Tipe & Kondisi</th>
                        <th>Alasan</th>
                        <th>Status</th>
                        <th style="width: 100px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returnItems as $return)
                        <tr>
                            <td>{{ $return->created_at->format('d/m/Y H:i') }} WIB</td>
                            <td>
                                <div style="font-weight: 500; color: var(--text-primary);">{{ $return->sale->invoice_number ?? '-' }}</div>
                            </td>
                            <td>{{ $return->product->name ?? '-' }}</td>
                            <td style="text-align: center;">{{ $return->quantity }}</td>
                            <td>
                                <div style="font-size: 0.85rem;">
                                    <span style="color: var(--accent);">{{ $return->return_type === 'refund' ? 'Kembali Uang' : 'Tukar Barang' }}</span><br>
                                    <span style="color: var(--text-muted);">{{ $return->item_condition === 'good' ? 'Kondisi Bagus' : 'Barang Rusak' }}</span>
                                </div>
                            </td>
                            <td>{{ $return->reason ?: '-' }}</td>
                            <td>
                                @if($return->status === 'pending')
                                    <span class="badge badge-warning">Menunggu Diproses</span>
                                @elseif($return->status === 'approved')
                                    <span class="badge badge-kasir">Disetujui (Stok Kembali)</span>
                                @elseif($return->status === 'rejected')
                                    <span class="badge" style="background: rgba(239, 68, 68, 0.1); color: var(--danger);">Ditolak</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <a href="{{ route('kepala-cabang.returns.show', $return->id) }}" class="btn btn-secondary btn-sm" style="padding: 0.375rem 0.75rem;">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem;">Belum ada riwayat retur barang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $returnItems->links() }}
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




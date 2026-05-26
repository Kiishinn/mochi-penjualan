@extends('layouts.dashboard')

@section('title', 'Perpindahan Barang')
@section('page-title', 'Perpindahan Barang')

@section('content')
    <div class="card" style="margin-bottom: 1.5rem;">
        <div class="card-header" style="flex-wrap: wrap; gap: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <h3>Manajemen Permintaan Stok</h3>
                <a href="{{ route('kepala-cabang.stock-transfers.create') }}" class="btn btn-primary btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Buat Permintaan Baru
                </a>
            </div>
            
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center; margin-left: auto;">
                <form id="filterForm" method="GET" action="{{ route('kepala-cabang.stock-transfers.index') }}" style="display: flex; gap: 0.5rem;">
                    <input type="hidden" name="tab" value="{{ $tab ?? 'outgoing' }}">
                    <select name="status" class="form-control" style="width: auto; padding: 0.25rem 0.5rem;" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui/Dikirim</option>
                        <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Diterima</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                    <input type="text" id="searchInput" name="search" class="form-control" placeholder="Cari produk..." value="{{ request('search') }}" style="width: 200px; padding: 0.25rem 0.5rem;" autocomplete="off">
                    @if(request('search') != '' || request('status') != '')
                        <a href="{{ route('kepala-cabang.stock-transfers.index', ['tab' => $tab ?? 'outgoing']) }}" class="btn btn-secondary btn-sm" style="background: transparent; color: var(--danger); border-color: var(--danger);">Reset</a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Custom Tabs -->
        <div style="display: flex; gap: 1rem; padding: 0 1.5rem; border-bottom: 1px solid var(--border-color); margin-bottom: 1.5rem; margin-top: -0.5rem;">
            <a href="{{ route('kepala-cabang.stock-transfers.index', ['tab' => 'outgoing']) }}" 
               style="padding: 1rem 0; color: {{ ($tab ?? 'outgoing') === 'outgoing' ? 'var(--accent)' : 'var(--text-secondary)' }}; font-weight: 500; text-decoration: none; border-bottom: 2px solid {{ ($tab ?? 'outgoing') === 'outgoing' ? 'var(--accent)' : 'transparent' }}; display: flex; align-items: center; gap: 0.5rem;">
                Permintaan Keluar (Saya Minta)
                @if($approvedOutgoing > 0)
                    <span style="background: var(--success); color: white; border-radius: 50%; width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: bold;">{{ $approvedOutgoing }}</span>
                @endif
            </a>
            <a href="{{ route('kepala-cabang.stock-transfers.index', ['tab' => 'incoming']) }}" 
               style="padding: 1rem 0; color: {{ ($tab ?? 'outgoing') === 'incoming' ? 'var(--accent)' : 'var(--text-secondary)' }}; font-weight: 500; text-decoration: none; border-bottom: 2px solid {{ ($tab ?? 'outgoing') === 'incoming' ? 'var(--accent)' : 'transparent' }}; display: flex; align-items: center; gap: 0.5rem;">
                Permintaan Masuk (Diminta dari Saya)
                @if($pendingIncoming > 0)
                    <span style="background: var(--danger); color: white; border-radius: 50%; width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: bold;">{{ $pendingIncoming }}</span>
                @endif
            </a>
        </div>

        <div style="overflow-x: auto; padding: 0 1.5rem 1.5rem;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>{{ ($tab ?? 'outgoing') === 'outgoing' ? 'Minta Ke Cabang' : 'Peminta' }}</th>
                        <th>Produk</th>
                        <th style="text-align: center;">Jumlah Diminta</th>
                        <th style="text-align: center;">Dikirim</th>
                        <th>Status</th>
                        <th style="width: 120px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transfers as $transfer)
                        <tr>
                            <td>
                                <div>{{ \Carbon\Carbon::parse($transfer->date)->format('d M Y') }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">{{ \Carbon\Carbon::parse($transfer->date)->format('H:i') }} WIB</div>
                            </td>
                            <td>
                                @if(($tab ?? 'outgoing') === 'outgoing')
                                    {{ $transfer->fromBranch->name ?? '-' }}
                                @else
                                    {{ $transfer->toBranch->name ?? '-' }}
                                @endif
                            </td>
                            <td>
                                <div style="font-weight: 500; color: var(--text-primary);">{{ $transfer->product->name ?? '-' }}</div>
                            </td>
                            <td style="text-align: center;">{{ $transfer->quantity_requested }}</td>
                            <td style="text-align: center;">{{ $transfer->quantity_sent ?? '-' }}</td>
                            <td>
                                @if($transfer->status === 'pending')
                                    <span class="badge badge-warning">Menunggu</span>
                                @elseif($transfer->status === 'approved')
                                    <span class="badge badge-kasir">Disetujui/Dikirim</span>
                                @elseif($transfer->status === 'received')
                                    <span class="badge badge-owner">Diterima</span>
                                @else
                                    <span class="badge" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">Ditolak</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <a href="{{ route('kepala-cabang.stock-transfers.edit', $transfer->id) }}" class="btn btn-sm" style="background: rgba(6, 182, 212, 0.1); color: var(--accent); padding: 4px 8px; text-decoration: none; border-radius: 4px;">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem;">Belum ada riwayat permintaan pada tab ini.</td>
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
                });
            }
        });
    </script>
@endsection

@extends('layouts.dashboard')

@section('title', 'Data Supplier')
@section('page-title', 'Data Supplier')

@section('content')
    <div class="card">
        <div class="card-header" style="flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <h3>Daftar Supplier</h3>
                <a href="{{ route('kepala-cabang.suppliers.create') }}" class="btn btn-primary btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Tambah Supplier
                </a>
            </div>
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center; margin-left: auto;">
                <form id="filterForm" method="GET" action="{{ route('kepala-cabang.suppliers.index') }}" style="display: flex; gap: 0.5rem;">
                    <input type="text" id="searchInput" name="search" class="form-control" placeholder="Cari supplier..." value="{{ request('search') }}" style="width: 250px; padding: 0.25rem 0.5rem;" autocomplete="off">
                    @if(request('search') != '')
                        <a href="{{ route('kepala-cabang.suppliers.index') }}" class="btn btn-secondary btn-sm" style="background: transparent; color: var(--danger); border-color: var(--danger);">Reset</a>
                    @endif
                </form>
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Perusahaan / Supplier</th>
                        <th>Kontak PIC</th>
                        <th>Pembayaran</th>
                        <th style="text-align: center;">Total Restock</th>
                        <th style="text-align: center;">Status</th>
                        <th style="width: 100px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $index => $supplier)
                        <tr>
                            <td>{{ $suppliers->firstItem() + $index }}</td>
                            <td>
                                <div style="font-weight: 500; color: var(--text-primary);">{{ $supplier->name }}</div>
                                <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 2px;">{{ $supplier->address ?? 'Alamat belum diisi' }}</div>
                            </td>
                            <td>
                                <div>{{ $supplier->contact_person ?? 'Tanpa PIC' }}</div>
                                <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 2px;">{{ $supplier->phone ?? '-' }}</div>
                            </td>
                            <td>
                                <div style="font-size: 0.9rem;">{{ $supplier->bank_account ?? 'Belum ada info' }}</div>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge" style="background: rgba(6, 182, 212, 0.1); color: var(--accent);">{{ $supplier->stock_ins_count }}x Transaksi</span>
                            </td>
                            <td style="text-align: center;">
                                @if($supplier->is_active)
                                    <span class="badge" style="background: rgba(34, 197, 94, 0.1); color: var(--success);">Aktif</span>
                                @else
                                    <span class="badge" style="background: rgba(148, 163, 184, 0.1); color: var(--text-muted);">Non-aktif</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <a href="{{ route('kepala-cabang.suppliers.edit', $supplier->id) }}" class="btn btn-secondary btn-sm" style="padding: 0.375rem 0.75rem;">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem;">Belum ada data supplier.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $suppliers->links() }}
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




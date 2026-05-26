@extends('layouts.dashboard')

@section('title', 'Data Supplier')
@section('page-title', 'Data Supplier')

@section('content')
    <div class="card">
        <div class="card-header" style="flex-wrap: wrap; gap: 1rem;">
            <h3>Daftar Supplier</h3>
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center; margin-left: auto;">
                <form id="filterForm" method="GET" action="{{ route('suppliers.index') }}" style="display: flex; gap: 0.5rem;">
                    <input type="text" id="searchInput" name="search" class="form-control" placeholder="Cari supplier..." value="{{ request('search') }}" style="width: 250px; padding: 0.25rem 0.5rem;" autocomplete="off">
                    @if(request('search') != '')
                        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary btn-sm" style="background: transparent; color: var(--danger); border-color: var(--danger);">Reset</a>
                    @endif
                </form>
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Supplier</th>
                        <th>No HP / Telepon</th>
                        <th>Alamat</th>
                        <th style="text-align: center;">Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $index => $supplier)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><div style="font-weight: 500; color: var(--text-primary);">{{ $supplier->name }}</div></td>
                            <td>{{ $supplier->phone ?? '-' }}</td>
                            <td>{{ $supplier->address ?? '-' }}</td>
                            <td style="text-align: center;">
                                @if($supplier->is_active)
                                    <span class="badge" style="background: rgba(34, 197, 94, 0.1); color: var(--success);">Aktif</span>
                                @else
                                    <span class="badge" style="background: rgba(239, 68, 68, 0.1); color: var(--danger);">Nonaktif</span>
                                @endif
                            </td>
                            <td>{{ $supplier->remarks ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem;">Belum ada data supplier.</td>
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




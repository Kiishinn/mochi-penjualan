@extends('layouts.dashboard')

@section('title', 'Data Stok Keluar')
@section('page-title', 'Data Stok Keluar')

@section('content')
    <div class="card">
        <div class="card-header" style="flex-wrap: wrap; gap: 1rem;">
            <h3>Riwayat Stok Keluar (Seluruh Cabang)</h3>
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center; margin-left: auto;">
                <form id="filterForm" method="GET" action="{{ route('outgoing-stocks.index') }}" style="display: flex; gap: 0.5rem;">
                    <select name="branch_id" class="form-control" style="width: auto; padding: 0.25rem 2rem 0.25rem 0.5rem;" onchange="this.form.submit()">
                        <option value="">Semua Cabang</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                    <input type="text" id="searchInput" name="search" class="form-control" placeholder="Cari produk/keterangan..." value="{{ request('search') }}" style="width: 250px; padding: 0.25rem 0.5rem;" autocomplete="off">
                    @if(request()->hasAny(['search', 'branch_id']) && (request('search') != '' || request('branch_id') != ''))
                        <a href="{{ route('outgoing-stocks.index') }}" class="btn btn-secondary btn-sm" style="background: transparent; color: var(--danger); border-color: var(--danger);">Reset</a>
                    @endif
                </form>
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Cabang</th>
                        <th>Produk</th>
                        <th style="text-align: center;">Jumlah</th>
                        <th>Alasan</th>
                        <th>User</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stockOuts as $out)
                        <tr>
                            <td>{{ date('d/m/Y', strtotime($out->date)) }}</td>
                            <td>{{ $out->branch->name ?? '-' }}</td>
                            <td>{{ $out->product->name ?? '-' }}</td>
                            <td style="text-align: center;">
                                <span class="badge" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">-{{ $out->quantity }}</span>
                            </td>
                            <td>{{ $out->reason ?? '-' }}</td>
                            <td>{{ $out->creator->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem;">Belum ada riwayat stok keluar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $stockOuts->links() }}
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




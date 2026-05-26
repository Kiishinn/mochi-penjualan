@extends('layouts.dashboard')

@section('title', 'Data Transaksi')
@section('page-title', 'Data Transaksi')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>Riwayat Transaksi Penjualan (Seluruh Cabang)</h3>
            <div>
                <form id="filterForm" method="GET" action="{{ route('transactions.index') }}" style="display: flex; gap: 0.5rem; width: 100%;">
                    <select name="branch_id" class="form-control" onchange="this.form.submit()">
                        <option value="">Semua Cabang</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                    <input type="text" id="searchInput" name="search" class="form-control" placeholder="Cari no. kuitansi/kasir..." value="{{ request('search') }}" autocomplete="off">
                    @if(request()->hasAny(['search', 'branch_id']) && (request('search') != '' || request('branch_id') != ''))
                        <a href="{{ route('transactions.index') }}" class="btn btn-secondary btn-sm" style="background: transparent; color: var(--danger); border-color: var(--danger);">Reset</a>
                    @endif
                </form>
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>No Invoice</th>
                        <th>Cabang</th>
                        <th>Kasir</th>
                        <th>Total Item</th>
                        <th style="text-align: right;">Total Transaksi</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr>
                            <td>{{ date('d/m/Y H:i', strtotime($sale->transaction_date)) }}</td>
                            <td>
                                <div style="font-weight: 500; color: var(--text-primary);">{{ $sale->invoice_number }}</div>
                            </td>
                            <td>{{ $sale->branch->name ?? '-' }}</td>
                            <td>{{ $sale->user->name ?? '-' }}</td>
                            <td>{{ $sale->details->sum('quantity') }}</td>
                            <td style="text-align: right; font-weight: 600; color: var(--accent);">
                                Rp {{ number_format($sale->total_price, 0, ',', '.') }}
                            </td>
                            <td style="text-align: center;">
                                <a href="{{ route('transactions.show', $sale->id) }}" class="btn btn-sm btn-secondary" title="Lihat Detail Transaksi">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem;">Belum ada transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $sales->links() }}
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




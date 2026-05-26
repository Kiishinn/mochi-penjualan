@extends('layouts.dashboard')

@section('title', 'Data Produk')
@section('page-title', 'Data Produk')

@section('content')
    <div class="card">
        <div class="card-header" style="flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <h3>Daftar Produk</h3>
                <a href="{{ route('kepala-cabang.products.create') }}" class="btn btn-primary btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Tambah Produk
                </a>
            </div>
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center; margin-left: auto;">
                <form id="filterForm" method="GET" action="{{ route('kepala-cabang.products.index') }}" style="display: flex; gap: 0.5rem;">
                    <input type="text" id="searchInput" name="search" class="form-control" placeholder="Cari nama produk..." value="{{ request('search') }}" style="width: 250px; padding: 0.25rem 0.5rem;" autocomplete="off">
                    @if(request('search') != '')
                        <a href="{{ route('kepala-cabang.products.index') }}" class="btn btn-secondary btn-sm" style="background: transparent; color: var(--danger); border-color: var(--danger);">Reset</a>
                    @endif
                </form>
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Satuan</th>
                        <th style="text-align: right;">Harga Beli</th>
                        <th style="text-align: right;">Harga Jual</th>
                        <th style="text-align: center;">Min Stok</th>
                        <th style="text-align: center;">Stok (Cabang Ini)</th>
                        <th style="width: 100px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>
                                <div style="font-weight: 500; color: var(--text-primary);">{{ $product->name }}</div>
                            </td>
                            <td>{{ $product->category->name ?? '-' }}</td>
                            <td>{{ $product->unit->name ?? '-' }}</td>
                            <td style="text-align: right;">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</td>
                            <td style="text-align: right;">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                            <td style="text-align: center;">{{ $product->minimum_stock }}</td>
                            <td style="text-align: center; font-weight: bold; color: var(--accent);">
                                {{ $stocks[$product->id] ?? 0 }}
                            </td>
                            <td style="text-align: center;">
                                <a href="{{ route('kepala-cabang.products.edit', $product->id) }}" class="btn btn-secondary btn-sm" style="padding: 0.375rem 0.75rem;">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 2rem;">Belum ada data produk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $products->links() }}
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




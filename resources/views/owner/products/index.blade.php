@extends('layouts.dashboard')

@section('title', 'Data Produk')
@section('page-title', 'Data Produk')

@section('content')
    <div class="card">
        <div class="card-header" style="flex-wrap: wrap; gap: 1rem;">
            <h3>Daftar Produk (Global)</h3>
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center; margin-left: auto;">
                <form id="filterForm" method="GET" action="{{ route('products.index') }}" style="display: flex; gap: 0.5rem;">
                    <input type="text" id="searchInput" name="search" class="form-control" placeholder="Cari nama produk..." value="{{ request('search') }}" style="width: 250px; padding: 0.25rem 0.5rem;" autocomplete="off">
                    @if(request('search') != '')
                        <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm" style="background: transparent; color: var(--danger); border-color: var(--danger);">Reset</a>
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
                        <th style="text-align: center;">Min. Stok</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>
                                <div style="font-weight: 500; color: var(--text-primary);">{{ $product->name }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $product->description }}</div>
                            </td>
                            <td>{{ $product->category->name ?? '-' }}</td>
                            <td>{{ $product->unit->name ?? '-' }}</td>
                            <td style="text-align: right;">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</td>
                            <td style="text-align: right;">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                            <td style="text-align: center;">
                                <span class="badge badge-warning">
                                    {{ $product->minimum_stock }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem;">Belum ada data produk.</td>
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




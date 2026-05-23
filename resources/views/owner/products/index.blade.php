@extends('layouts.dashboard')

@section('title', 'Data Produk')
@section('page-title', 'Data Produk')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>Daftar Produk (Global)</h3>
        </div>

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Barcode</th>
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
                            <td>{{ $product->barcode ?? '-' }}</td>
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
@endsection

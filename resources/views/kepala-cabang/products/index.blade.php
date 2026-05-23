@extends('layouts.dashboard')

@section('title', 'Data Produk')
@section('page-title', 'Data Produk')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>Daftar Produk</h3>
            <a href="{{ route('kepala-cabang.products.create') }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                Tambah Produk
            </a>
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
                        <th style="text-align: center;">Min Stok</th>
                        <th style="text-align: center;">Stok (Cabang Ini)</th>
                        <th style="width: 100px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>{{ $product->barcode ?? '-' }}</td>
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
@endsection

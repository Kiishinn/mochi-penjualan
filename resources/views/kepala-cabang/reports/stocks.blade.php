@extends('layouts.dashboard')

@section('title', 'Laporan Stok')
@section('page-title', 'Laporan Stok')

@section('content')
    <div class="card" style="margin-bottom: 1.5rem;">
        <div class="card-header" style="justify-content: flex-start; gap: 1rem;">
            <h3>Laporan Ketersediaan Stok (Cabang Ini)</h3>
            <button type="button" class="btn btn-secondary btn-sm" onclick="window.print()">Cetak Laporan</button>
        </div>
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Barcode</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Satuan</th>
                        <th style="text-align: center;">Min. Stok</th>
                        <th style="text-align: center;">Stok Aktual</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stocks as $stock)
                        <tr>
                            <td>{{ $stock->product->barcode ?? '-' }}</td>
                            <td>{{ $stock->product->name ?? '-' }}</td>
                            <td>{{ $stock->product->category->name ?? '-' }}</td>
                            <td>{{ $stock->product->unit->name ?? '-' }}</td>
                            <td style="text-align: center;">{{ $stock->product->minimum_stock }}</td>
                            <td style="text-align: center; font-weight: 700; font-size: 1rem;">{{ $stock->quantity }}</td>
                            <td>
                                @if($stock->quantity == 0)
                                    <span class="badge" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">Habis</span>
                                @elseif($stock->quantity <= $stock->product->minimum_stock)
                                    <span class="badge badge-warning">Menipis</span>
                                @else
                                    <span class="badge badge-kasir">Aman</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem;">Belum ada data stok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

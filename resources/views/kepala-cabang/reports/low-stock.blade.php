@extends('layouts.dashboard')

@section('title', 'Peringatan Stok Kritis')
@section('page-title', 'Peringatan Stok Kritis (Low Stock)')

@section('content')
    <div class="card">
        <div class="card-header" style="background: rgba(239, 68, 68, 0.05);">
            <h3 style="color: var(--danger);">Daftar Barang Harus Segera Direstock</h3>
        </div>
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th style="text-align: center;">Batas Minimum</th>
                        <th style="text-align: center;">Sisa Stok</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lowStocks as $stock)
                        <tr style="background: {{ $stock->quantity == 0 ? 'rgba(239,68,68,0.05)' : 'rgba(245,158,11,0.05)' }};">
                            <td>
                                <div style="font-weight: 600; color: var(--text-primary);">{{ $stock->product->name ?? '-' }}</div>
                            </td>
                            <td>{{ $stock->product->category->name ?? '-' }}</td>
                            <td style="text-align: center;">
                                <span class="badge" style="background: rgba(148, 163, 184, 0.2); color: var(--text-primary);">
                                    {{ $stock->product->minimum_stock }} {{ $stock->product->unit->name ?? '' }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                @if($stock->quantity == 0)
                                    <span class="badge" style="background: rgba(239, 68, 68, 0.1); color: var(--danger); font-size: 1rem; font-weight: 700;">
                                        HABIS (0)
                                    </span>
                                @else
                                    <span class="badge" style="background: rgba(245, 158, 11, 0.1); color: var(--warning); font-size: 1rem; font-weight: 700;">
                                        SISA {{ $stock->quantity }}
                                    </span>
                                @endif
                            </td>
                            <td style="text-align: center; display: flex; gap: 0.5rem; justify-content: center;">
                                <a href="{{ route('kepala-cabang.stock-ins.create') }}" class="btn btn-primary btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">+ Restock</a>
                                <a href="{{ route('kepala-cabang.reports.stock-card', $stock->product_id) }}" class="btn btn-secondary btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Kartu Stok</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 3rem;">
                                <div style="color: var(--success); font-size: 1.25rem; font-weight: 600;">Aman!</div>
                                <p style="color: var(--text-muted); margin-top: 0.5rem;">Tidak ada stok yang menipis di cabang Anda. Semua barang dalam kondisi aman.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

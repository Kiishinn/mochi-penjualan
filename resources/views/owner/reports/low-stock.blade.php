@extends('layouts.dashboard')

@section('title', 'Peringatan Stok Kritis')
@section('page-title', 'Peringatan Stok Kritis (Low Stock)')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h3>Filter Cabang</h3>
        </div>
        <form action="{{ route('reports.low-stock') }}" method="GET" class="form-grid">
            <div class="form-group">
                <label for="branch_id">Cabang</label>
                <select id="branch_id" name="branch_id" class="form-control">
                    <option value="">Semua Cabang</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-actions" style="grid-column: 1 / -1; margin-top: 0;">
                <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                <a href="{{ route('reports.low-stock') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-header" style="background: rgba(239, 68, 68, 0.05);">
            <h3 style="color: var(--danger);">Daftar Barang Harus Segera Direstock</h3>
        </div>
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Cabang</th>
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
                            <td><div style="font-weight: 500;">{{ $stock->branch->name ?? '-' }}</div></td>
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
                            <td style="text-align: center;">
                                <a href="{{ route('reports.stock-card', ['branch_id' => $stock->branch_id, 'product_id' => $stock->product_id]) }}" class="btn btn-secondary btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Kartu Stok</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 3rem;">
                                <div style="color: var(--success); font-size: 1.25rem; font-weight: 600;">Hebat!</div>
                                <p style="color: var(--text-muted); margin-top: 0.5rem;">Tidak ada stok yang menipis. Semua barang dalam kondisi aman.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

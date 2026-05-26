@extends('layouts.dashboard')

@section('title', 'Laporan Stok')
@section('page-title', 'Laporan Stok')

@section('content')
    <div class="card" style="margin-bottom: 1.5rem;">
        <div class="card-header" style="justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <h3 style="margin: 0;">Laporan Ketersediaan Stok (Cabang Ini)</h3>
                <button type="button" class="btn btn-secondary btn-sm no-print" onclick="window.print()">Cetak Laporan</button>
            </div>
            <form action="{{ route('kepala-cabang.reports.stocks') }}" method="GET" class="no-print" style="display: flex; gap: 0.5rem; align-items: center;">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari nama barang..." style="width: 250px;">
                <select name="status" class="form-control">
                    <option value="">Semua Status</option>
                    <option value="aman" {{ request('status') == 'aman' ? 'selected' : '' }}>Aman</option>
                    <option value="menipis" {{ request('status') == 'menipis' ? 'selected' : '' }}>Menipis</option>
                    <option value="habis" {{ request('status') == 'habis' ? 'selected' : '' }}>Habis</option>
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
                @if(request('search') || request('status'))
                    <a href="{{ route('kepala-cabang.reports.stocks') }}" class="btn btn-secondary">Reset</a>
                @endif
            </form>
        </div>
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Satuan</th>
                        <th style="text-align: center;">Min. Stok</th>
                        <th style="text-align: center;">Stok Aktual</th>
                        <th>Status</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stocks as $stock)
                        <tr>
                            <td><div style="font-weight: 500;">{{ $stock->product->name }}</div></td>
                            <td>{{ $stock->product->category->name ?? '-' }}</td>
                            <td>{{ $stock->product->unit->name ?? '-' }}</td>
                            <td style="text-align: center;">{{ $stock->product->minimum_stock }}</td>
                            <td style="text-align: center;">
                                <div style="font-weight: 700; font-size: 1.1rem; color: {{ $stock->quantity <= $stock->product->minimum_stock ? 'var(--danger)' : 'var(--text-primary)' }};">{{ $stock->quantity }}</div>
                            </td>
                            <td>
                                @if($stock->quantity == 0)
                                    <span class="badge badge-danger">Habis</span>
                                @elseif($stock->quantity <= $stock->product->minimum_stock)
                                    <span class="badge badge-warning">Menipis</span>
                                @else
                                    <span class="badge badge-success">Aman</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <a href="{{ route('kepala-cabang.reports.stock-card', $stock->product_id) }}" class="btn btn-secondary btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Kartu Stok</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 2rem;">Belum ada data stok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4 no-print" style="padding: 1rem;">
                {{ $stocks->withQueryString()->links() }}
            </div>
        </div>
    </div>
@endsection

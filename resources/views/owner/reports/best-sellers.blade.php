@extends('layouts.dashboard')

@section('title', 'Produk Terlaris')
@section('page-title', 'Laporan Produk Terlaris')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h3>Filter Produk Terlaris</h3>
        </div>
        <form action="{{ route('reports.best-sellers') }}" method="GET" class="form-grid">
            <div class="form-group">
                <label for="start_date">Dari Tanggal</label>
                <input type="date" id="start_date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="form-group">
                <label for="end_date">Sampai Tanggal</label>
                <input type="date" id="end_date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
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
            <div class="form-actions" style="grid-column: 1 / -1;">
                <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                <a href="{{ route('reports.best-sellers') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Top 20 Produk Terlaris</h3>
        </div>
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">Peringkat</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Satuan</th>
                        <th style="text-align: center;">Total Terjual</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topProducts as $index => $item)
                        <tr>
                            <td>
                                <div style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 50%; background: var(--accent); color: white; font-weight: 600;">
                                    {{ $topProducts->firstItem() + $index }}
                                </div>
                            </td>
                            <td><div style="font-weight: 500; color: var(--text-primary);">{{ $item->product->name ?? '-' }}</div></td>
                            <td>{{ $item->product->category->name ?? '-' }}</td>
                            <td>{{ $item->product->unit->name ?? '-' }}</td>
                            <td style="text-align: center;">
                                <span class="badge" style="background: rgba(34, 197, 94, 0.1); color: var(--success); font-size: 1rem;">
                                    {{ $item->total_qty }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 2rem;">Tidak ada data penjualan pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $topProducts->links() }}
        </div>
    </div>
@endsection

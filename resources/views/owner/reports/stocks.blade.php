@extends('layouts.dashboard')

@section('title', 'Laporan Stok')
@section('page-title', 'Laporan Stok')

@section('content')
    <div class="card no-print" style="margin-bottom: 1.5rem;">
        <div class="card-header">
            <h3>Filter Laporan Stok</h3>
        </div>
        <form method="GET" action="{{ route('reports.stocks') }}" class="form-grid" style="align-items: flex-end;" id="filter-form">
            <div class="form-group" style="margin-bottom: 0;">
                <label>Pencarian</label>
                <div style="display: flex;">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari nama barang..." style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                    <button type="submit" class="btn btn-primary" style="border-top-left-radius: 0; border-bottom-left-radius: 0; padding: 0.5rem 1rem;">Cari</button>
                </div>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label>Cabang</label>
                <select name="branch_id" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Cabang</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" @if(request('branch_id') == $branch->id) selected @endif>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label>Status Stok</label>
                <select name="status" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="aman" @if(request('status') == 'aman') selected @endif>Aman</option>
                    <option value="menipis" @if(request('status') == 'menipis') selected @endif>Menipis</option>
                    <option value="habis" @if(request('status') == 'habis') selected @endif>Habis</option>
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                <a href="{{ route('reports.stocks') }}" class="btn btn-secondary">Reset</a>
                <button type="button" class="btn btn-secondary no-print" onclick="window.print()">Cetak / PDF</button>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="stat-grid no-print" style="margin-bottom: 1.5rem; margin-top: 0;">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(139, 92, 246, 0.1);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#8b5cf6"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" /></svg>
            </div>
            <div>
                <div class="stat-value" style="color: #8b5cf6;">{{ number_format($totalSku, 0, ',', '.') }}</div>
                <div class="stat-label">Total Item (SKU)</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(239, 68, 68, 0.1);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ef4444"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            </div>
            <div>
                <div class="stat-value" style="color: #ef4444;">{{ number_format($totalHabis, 0, ',', '.') }}</div>
                <div class="stat-label">Barang Habis</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#f59e0b"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            </div>
            <div>
                <div class="stat-value" style="color: #f59e0b;">{{ number_format($totalMenipis, 0, ',', '.') }}</div>
                <div class="stat-label">Barang Menipis</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(34, 197, 94, 0.1);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#22c55e"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
            </div>
            <div>
                <div class="stat-value" style="color: #22c55e;">Rp {{ number_format($totalAset, 0, ',', '.') }}</div>
                <div class="stat-label">Total Valuasi Aset</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Daftar Ketersediaan Stok</h3>
        </div>
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Cabang</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Satuan</th>
                        <th style="text-align: center;">Min. Stok</th>
                        <th style="text-align: center;">Stok Aktual</th>
                        <th style="text-align: right;">Valuasi Aset (Rp)</th>
                        <th>Status</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stocks as $stock)
                        <tr>
                            <td><div style="font-weight: 500;">{{ $stock->branch->name ?? '-' }}</div></td>
                            <td>{{ $stock->product->name ?? '-' }}</td>
                            <td>{{ $stock->product->category->name ?? '-' }}</td>
                            <td>{{ $stock->product->unit->name ?? '-' }}</td>
                            <td style="text-align: center;">{{ $stock->product->minimum_stock }}</td>
                            <td style="text-align: center;">
                                <div style="font-weight: 700; font-size: 1.1rem; color: {{ $stock->quantity <= $stock->product->minimum_stock ? 'var(--danger)' : 'var(--text-primary)' }};">{{ $stock->quantity }}</div>
                            </td>
                            <td style="text-align: right;">{{ number_format($stock->quantity * $stock->product->purchase_price, 0, ',', '.') }}</td>
                            <td>
                                @if($stock->quantity == 0)
                                    <span class="badge" style="background: rgba(239, 68, 68, 0.1); color: var(--danger);">Habis</span>
                                @elseif($stock->quantity <= $stock->product->minimum_stock)
                                    <span class="badge" style="background: rgba(245, 158, 11, 0.1); color: var(--warning);">Menipis</span>
                                @else
                                    <span class="badge" style="background: rgba(34, 197, 94, 0.1); color: var(--success);">Aman</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <a href="{{ route('reports.stock-card', ['branch_id' => $stock->branch_id, 'product_id' => $stock->product_id]) }}" class="btn btn-secondary btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Kartu Stok</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" style="text-align: center; padding: 2rem;">Belum ada data stok yang sesuai dengan filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $stocks->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection

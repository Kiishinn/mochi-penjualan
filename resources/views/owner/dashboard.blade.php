@extends('layouts.dashboard')

@section('title', 'Dashboard Owner')
@section('page-title', 'Dashboard')

@section('content')
    <div style="margin-bottom: 2rem;">
        <h2 class="welcome-text">Selamat Datang, {{ Auth::user()->name }}!</h2>
        <p class="welcome-sub">Berikut adalah ringkasan performa Mochi Petshop secara keseluruhan.</p>
    </div>

    <!-- Ringkasan Statistik -->
    <div class="stat-grid" style="margin-bottom: 2rem;">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(139, 92, 246, 0.1);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#a78bfa"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" /></svg>
            </div>
            <div>
                <div class="stat-value" style="color: #a78bfa;">{{ $totalBranches }}</div>
                <div class="stat-label">Total Cabang</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#f59e0b"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" /></svg>
            </div>
            <div>
                <div class="stat-value" style="color: #f59e0b;">{{ $totalProducts }}</div>
                <div class="stat-label">Total Produk</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(6, 182, 212, 0.1);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#06b6d4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" /></svg>
            </div>
            <div>
                <div class="stat-value" style="color: #06b6d4;">{{ $totalSales }}</div>
                <div class="stat-label">Total Transaksi</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(34, 197, 94, 0.1);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#22c55e"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
            </div>
            <div>
                <div class="stat-value" style="color: #22c55e;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                <div class="stat-label">Total Pendapatan</div>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem;">
        
        <!-- Penjualan Per Cabang -->
        <div class="card">
            <div class="card-header">
                <h3>Performa Cabang</h3>
            </div>
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nama Cabang</th>
                            <th style="text-align: center;">Transaksi</th>
                            <th style="text-align: right;">Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salesPerBranch as $branch)
                            <tr>
                                <td><div style="font-weight: 500; color: var(--text-primary);">{{ $branch->name }}</div></td>
                                <td style="text-align: center;">
                                    <span class="badge badge-kasir">{{ $branch->sales_count }}</span>
                                </td>
                                <td style="text-align: right; color: var(--accent); font-weight: 600;">
                                    Rp {{ number_format($branch->sales_sum_total_price ?? 0, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Peringatan Stok -->
        <div class="card">
            <div class="card-header">
                <h3>Peringatan Stok Menipis</h3>
            </div>
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Cabang</th>
                            <th>Produk</th>
                            <th style="text-align: center;">Sisa Stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lowStocks as $stock)
                            <tr>
                                <td>{{ $stock->branch->name ?? '-' }}</td>
                                <td><div style="font-weight: 500; color: var(--text-primary);">{{ $stock->product->name ?? '-' }}</div></td>
                                <td style="text-align: center;">
                                    <span class="badge" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                                        {{ $stock->quantity }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; padding: 1rem; color: var(--success);">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 32px; height: 32px; margin: 0 auto 0.5rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                    <br>Semua stok produk di semua cabang aman.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Performa Kasir (Top Sales Bulan Ini - Semua Cabang) -->
    <div class="card" style="margin-top: 2rem;">
        <div class="card-header">
            <h3>Top Performa Kasir (Seluruh Cabang - Bulan Ini)</h3>
            <a href="{{ route('shifts.index') }}" class="btn btn-secondary btn-sm">Lihat Shift Kasir</a>
        </div>
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Peringkat</th>
                        <th>Nama Kasir</th>
                        <th>Cabang</th>
                        <th style="text-align: center;">Jumlah Transaksi</th>
                        <th style="text-align: right;">Total Penjualan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cashierPerformances as $index => $kasir)
                        <tr>
                            <td style="font-weight: bold; color: var(--text-muted);">#{{ $index + 1 }}</td>
                            <td>
                                <div style="font-weight: 500; color: var(--text-primary);">{{ $kasir->name }}</div>
                            </td>
                            <td>{{ $kasir->branch->name ?? '-' }}</td>
                            <td style="text-align: center;">{{ $kasir->transactions_count }}x</td>
                            <td style="text-align: right; font-weight: 600; color: var(--success);">
                                Rp {{ number_format($kasir->total_revenue, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                                Belum ada data penjualan dari kasir bulan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
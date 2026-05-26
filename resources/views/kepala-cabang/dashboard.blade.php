@extends('layouts.dashboard')

@section('title', 'Dashboard Kepala Cabang')
@section('page-title', 'Dashboard')

@section('content')
    <div style="margin-bottom: 2rem;">
        <h2 class="welcome-text">Selamat Datang, {{ Auth::user()->name }}!</h2>
        <p class="welcome-sub">Cabang: <span style="color: var(--accent); font-weight: 600;">{{ Auth::user()->branch->name ?? '-' }}</span></p>
    </div>

    <!-- Ringkasan Statistik Hari Ini -->
    <div class="stat-grid" style="margin-bottom: 2rem;">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#f59e0b"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" /></svg>
            </div>
            <div>
                <div class="stat-value" style="color: #f59e0b;">{{ $totalProducts }}</div>
                <div class="stat-label">Macam Produk Tersedia</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(6, 182, 212, 0.1);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#06b6d4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" /></svg>
            </div>
            <div>
                <div class="stat-value" style="color: #06b6d4;">{{ $salesToday }}</div>
                <div class="stat-label">Transaksi Hari Ini</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(34, 197, 94, 0.1);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#22c55e"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
            </div>
            <div>
                <div class="stat-value" style="color: #22c55e;">Rp {{ number_format($revenueTodayVal, 0, ',', '.') }}</div>
                <div class="stat-label">Pendapatan Hari Ini</div>
            </div>
        </div>
    </div>

    <!-- Peringatan Stok Menipis -->
    <div class="card">
        <div class="card-header">
            <h3>Peringatan Stok Menipis (Cabang Ini)</h3>
            <a href="{{ route('kepala-cabang.stock-ins.create') }}" class="btn btn-primary btn-sm">Stok Masuk</a>
        </div>
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama Produk</th>
                        <th style="text-align: center;">Min. Stok</th>
                        <th style="text-align: center;">Sisa Stok</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lowStocks as $stock)
                        <tr>
                            <td><div style="font-weight: 500; color: var(--text-primary);">{{ $stock->product->name ?? '-' }}</div></td>
                            <td style="text-align: center;">{{ $stock->product->minimum_stock }}</td>
                            <td style="text-align: center;">
                                <span class="badge" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                                    {{ $stock->quantity }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 2rem; color: var(--success);">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 32px; height: 32px; margin: 0 auto 0.5rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                <br>Stok produk di cabang Anda terpantau aman.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Performa Kasir (Top Sales Bulan Ini) -->
    <div class="card" style="margin-top: 2rem;">
        <div class="card-header">
            <h3>Performa Kasir (Bulan Ini)</h3>
        </div>
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Peringkat</th>
                        <th>Nama Kasir</th>
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
                            <td style="text-align: center;">{{ $kasir->transactions_count }}x</td>
                            <td style="text-align: right; font-weight: 600; color: var(--success);">
                                Rp {{ number_format($kasir->total_revenue, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                                Belum ada data penjualan dari kasir bulan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@extends('layouts.dashboard')

@section('title', 'Dashboard Kasir')
@section('page-title', 'Dashboard')

@section('content')
    <div style="margin-bottom: 2rem;">
        <h2 class="welcome-text">Selamat Datang, {{ Auth::user()->name }}!</h2>
        <p class="welcome-sub">Cabang: <span style="color: var(--accent); font-weight: 600;">{{ Auth::user()->branch->name ?? '-' }}</span></p>
    </div>

    @php
        $currentShift = \App\Models\Shift::where('user_id', Auth::id())->where('status', 'open')->first();
    @endphp

    @if($currentShift)
        <div class="alert" style="background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2); border-radius: 12px; padding: 1rem 1.5rem; margin-bottom: 2rem; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 40px; height: 40px; background: rgba(34, 197, 94, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#22c55e" style="width: 20px; height: 20px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <div>
                    <h4 style="margin: 0; color: #166534; font-size: 1rem;">Shift Aktif</h4>
                    <p style="margin: 0; color: #15803d; font-size: 0.85rem;">Dimulai: {{ $currentShift->start_time->format('H:i') }} | Modal Laci: Rp {{ number_format($currentShift->starting_cash, 0, ',', '.') }}</p>
                </div>
            </div>
            <a href="{{ route('kasir.shifts.close') }}" class="btn" style="background: white; color: var(--danger); border: 1px solid var(--danger); padding: 0.5rem 1rem;">
                Tutup Shift
            </a>
        </div>
    @else
        <div class="alert" style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 12px; padding: 1rem 1.5rem; margin-bottom: 2rem; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 40px; height: 40px; background: rgba(245, 158, 11, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#f59e0b" style="width: 20px; height: 20px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h4 style="margin: 0; color: #92400e; font-size: 1rem;">Shift Belum Dibuka</h4>
                    <p style="margin: 0; color: #b45309; font-size: 0.85rem;">Anda harus membuka shift sebelum dapat melayani transaksi.</p>
                </div>
            </div>
            <a href="{{ route('kasir.shifts.create') }}" class="btn btn-primary" style="padding: 0.5rem 1rem;">
                Buka Shift Sekarang
            </a>
        </div>
    @endif

    <!-- Ringkasan Statistik Hari Ini -->
    <div class="stat-grid" style="margin-bottom: 2rem;">
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

    <!-- Quick Actions -->
    <div style="display: flex; gap: 1rem;">
        @if($currentShift)
            <a href="{{ route('kasir.transactions.create') }}" class="card" style="flex: 1; text-align: center; text-decoration: none; padding: 2rem; display: flex; flex-direction: column; align-items: center; justify-content: center; transition: transform 0.2s, border-color 0.2s;">
        @else
            <a href="{{ route('kasir.shifts.create') }}" class="card" style="flex: 1; text-align: center; text-decoration: none; padding: 2rem; display: flex; flex-direction: column; align-items: center; justify-content: center; transition: transform 0.2s, border-color 0.2s;">
        @endif
            <div style="width: 64px; height: 64px; background: var(--accent-gradient); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#fff" style="width: 32px; height: 32px;"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" /></svg>
            </div>
            <h3 style="color: var(--text-primary); font-size: 1.125rem;">Mulai Kasir</h3>
            <p style="color: var(--text-muted); font-size: 0.875rem;">Masuk ke sistem Point of Sales (POS)</p>
        </a>

        <a href="{{ route('kasir.returns.create') }}" class="card" style="flex: 1; text-align: center; text-decoration: none; padding: 2rem; display: flex; flex-direction: column; align-items: center; justify-content: center; transition: transform 0.2s, border-color 0.2s;">
            <div style="width: 64px; height: 64px; background: rgba(239, 68, 68, 0.1); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ef4444" style="width: 32px; height: 32px;"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
            </div>
            <h3 style="color: var(--text-primary); font-size: 1.125rem;">Proses Retur</h3>
            <p style="color: var(--text-muted); font-size: 0.875rem;">Terima pengajuan retur dari pelanggan</p>
        </a>
    </div>

    <style>
        a.card:hover { transform: translateY(-4px); border-color: var(--accent); }
    </style>
@endsection

@extends('layouts.dashboard')

@section('title', 'Laporan Penjualan')
@section('page-title', 'Laporan Penjualan')

@section('content')
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/dark.css">
    <div class="card no-print" style="margin-bottom: 1.5rem;">
        <div class="card-header">
            <h3>Filter Laporan</h3>
        </div>
        <form method="GET" action="{{ route('reports.sales') }}" class="form-grid" style="align-items: flex-end;" id="filter-form">
            <div class="form-group" style="margin-bottom: 0;">
                <label>Tanggal Mulai</label>
                <input type="date" id="start_date" name="start_date" class="form-control" value="{{ request('start_date', date('Y-m-d')) }}">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label>Tanggal Akhir</label>
                <input type="date" id="end_date" name="end_date" class="form-control" value="{{ request('end_date', date('Y-m-d')) }}">
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
            
            <div style="display: flex; gap: 0.5rem; width: 100%; grid-column: 1 / -1; margin-bottom: 0.5rem; flex-wrap: wrap;">
                <span style="font-size: 0.875rem; color: var(--text-muted); align-self: center;">Pilih Cepat:</span>
                <button type="button" class="btn btn-sm btn-secondary" onclick="setQuickDate('today')">Hari Ini</button>
                <button type="button" class="btn btn-sm btn-secondary" onclick="setQuickDate('7days')">7 Hari Terakhir</button>
                <button type="button" class="btn btn-sm btn-secondary" onclick="setQuickDate('this_month')">Bulan Ini</button>
                <button type="button" class="btn btn-sm btn-secondary" onclick="setQuickDate('this_year')">Tahun Ini</button>
            </div>
            <div style="display: flex; gap: 0.5rem; margin-top: 1.5rem; flex-wrap: wrap;">
                <a href="{{ route('reports.sales') }}" class="btn btn-secondary">Reset</a>
                
                <!-- Print/PDF Button (Native) -->
                <button type="button" class="btn btn-secondary no-print" onclick="window.print()">Cetak / PDF</button>
            </div>
        </form>
    </div>

    <!-- Charts Section -->
    <div class="chart-grid no-print" style="gap: 1.5rem; margin-bottom: 1.5rem;">
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <h3>Tren Penjualan</h3>
            </div>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header">
                <h3>Kontribusi Cabang</h3>
            </div>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="branchChart"></canvas>
            </div>
        </div>
    </div>

    <div class="stat-grid no-print" style="margin-bottom: 1.5rem; margin-top: 0;">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(34, 197, 94, 0.1);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#22c55e"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
            </div>
            <div>
                <div class="stat-value" style="color: #22c55e;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                <div class="stat-label">Total Pendapatan</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(6, 182, 212, 0.1);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#06b6d4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" /></svg>
            </div>
            <div>
                <div class="stat-value" style="color: #06b6d4;">{{ number_format($totalTransactions, 0, ',', '.') }}</div>
                <div class="stat-label">Total Transaksi</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Detail Transaksi</h3>
        </div>
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>No Invoice</th>
                        <th>Cabang</th>
                        <th>Kasir</th>
                        <th>Produk Dibeli</th>
                        <th style="text-align: right;">Total Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr>
                            <td>{{ date('d/m/Y H:i', strtotime($sale->transaction_date)) }}</td>
                            <td><div style="font-weight: 500; color: var(--text-primary);">{{ $sale->invoice_number }}</div></td>
                            <td>{{ $sale->branch->name ?? '-' }}</td>
                            <td>{{ $sale->user->name ?? '-' }}</td>
                            <td>
                                @foreach($sale->details as $detail)
                                    <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.25rem;">
                                        <span style="font-weight: 600; color: var(--text-primary);">{{ $detail->quantity }}x</span> {{ $detail->product->name ?? 'Produk Dihapus' }} 
                                        <span style="font-size: 0.75rem;">(Rp {{ number_format($detail->price, 0, ',', '.') }} / item)</span>
                                    </div>
                                @endforeach
                            </td>
                            <td style="text-align: right; font-weight: 600; color: var(--accent);">Rp {{ number_format($sale->total_price, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem;">Belum ada data pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
    <script>
        // Inisialisasi Flatpickr agar input tanggal "bisa dipencet" dengan kalender yang rapi
        flatpickr("#start_date", {
            dateFormat: "Y-m-d",
            locale: "id",
            allowInput: true,
            onChange: function(selectedDates, dateStr, instance) {
                document.getElementById('filter-form').submit();
            }
        });
        flatpickr("#end_date", {
            dateFormat: "Y-m-d",
            locale: "id",
            allowInput: true,
            onChange: function(selectedDates, dateStr, instance) {
                document.getElementById('filter-form').submit();
            }
        });
        function setQuickDate(type) {
            const form = document.getElementById('filter-form');
            const start = document.getElementById('start_date');
            const end = document.getElementById('end_date');
            const today = new Date();
            
            // Adjust to local timezone explicitly to avoid UTC shift
            const formatDate = (date) => {
                const tzOffset = date.getTimezoneOffset() * 60000; // offset in milliseconds
                const localISOTime = (new Date(date - tzOffset)).toISOString().slice(0, 10);
                return localISOTime;
            };

            if (type === 'today') {
                start.value = formatDate(today);
                end.value = formatDate(today);
            } else if (type === '7days') {
                const past = new Date(today);
                past.setDate(today.getDate() - 7);
                start.value = formatDate(past);
                end.value = formatDate(today);
            } else if (type === 'this_month') {
                const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
                start.value = formatDate(firstDay);
                end.value = formatDate(today);
            } else if (type === 'this_year') {
                const firstDay = new Date(today.getFullYear(), 0, 1); // 1 Januari
                start.value = formatDate(firstDay);
                end.value = formatDate(today);
            }
            
            form.submit();
        }

        // Data for Trend Chart
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        const trendChart = new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartDates) !!},
                datasets: [{
                    label: 'Total Pendapatan (Rp)',
                    data: {!! json_encode($chartTotals) !!},
                    borderColor: '#0ea5e9',
                    backgroundColor: 'rgba(14, 165, 233, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(148, 163, 184, 0.1)' } },
                    x: { grid: { display: false } }
                }
            }
        });

        // Data for Branch Pie Chart
        const branchCtx = document.getElementById('branchChart').getContext('2d');
        const branchChart = new Chart(branchCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($pieLabels) !!},
                datasets: [{
                    data: {!! json_encode($pieData) !!},
                    backgroundColor: ['#22c55e', '#f59e0b', '#3b82f6', '#ec4899', '#8b5cf6'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { color: '#f1f5f9' } }
                }
            }
        });
    </script>
@endsection

@extends('layouts.dashboard')

@section('title', 'Data Transaksi')
@section('page-title', 'Data Transaksi')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>Riwayat Transaksi Penjualan (Seluruh Cabang)</h3>
        </div>

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>No Invoice</th>
                        <th>Cabang</th>
                        <th>Kasir</th>
                        <th>Total Item</th>
                        <th style="text-align: right;">Total Transaksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr>
                            <td>{{ date('d/m/Y H:i', strtotime($sale->transaction_date)) }}</td>
                            <td>
                                <div style="font-weight: 500; color: var(--text-primary);">{{ $sale->invoice_number }}</div>
                            </td>
                            <td>{{ $sale->branch->name ?? '-' }}</td>
                            <td>{{ $sale->user->name ?? '-' }}</td>
                            <td>{{ $sale->details->sum('quantity') }}</td>
                            <td style="text-align: right; font-weight: 600; color: var(--accent);">
                                Rp {{ number_format($sale->total_price, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem;">Belum ada transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $sales->links() }}
            </div>
        </div>
    </div>
@endsection

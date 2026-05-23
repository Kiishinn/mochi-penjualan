@extends('layouts.dashboard')

@section('title', 'Data Transaksi')
@section('page-title', 'Data Transaksi Penjualan')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>Semua Transaksi</h3>
        </div>

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>No. Invoice</th>
                        <th>Cabang</th>
                        <th>Kasir</th>
                        <th>Metode Bayar</th>
                        <th style="text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $trx)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($trx->date)->format('d M Y') }}</td>
                            <td><div style="font-weight: 500; color: var(--text-primary);">{{ $trx->invoice_number }}</div></td>
                            <td>{{ $trx->branch->name ?? '-' }}</td>
                            <td>{{ $trx->user->name ?? '-' }}</td>
                            <td>
                                <span class="badge badge-owner" style="text-transform: uppercase;">{{ $trx->payment_method }}</span>
                            </td>
                            <td style="text-align: right; font-weight: 600; color: var(--success);">
                                Rp {{ number_format($trx->total, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem;">Belum ada data transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

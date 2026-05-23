@extends('layouts.dashboard')

@section('title', 'Data Transaksi Kasir')
@section('page-title', 'Data Transaksi Kasir')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>Riwayat Transaksi</h3>
        </div>

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>No. Struk</th>
                        <th>Kasir</th>
                        <th>Metode Bayar</th>
                        <th>Status</th>
                        <th style="text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $trx)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($trx->date)->format('d M Y H:i') }}</td>
                            <td><div style="font-weight: 500; color: var(--text-primary);">{{ $trx->receipt_number }}</div></td>
                            <td>{{ $trx->user->name ?? '-' }}</td>
                            <td><span style="text-transform: capitalize;">{{ $trx->payment_method }}</span></td>
                            <td>
                                @php
                                    $badgeClass = match($trx->status) {
                                        'completed' => 'badge-kasir',
                                        'refunded' => 'badge-owner',
                                        default => 'badge-warning',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">
                                    {{ ucfirst($trx->status) }}
                                </span>
                            </td>
                            <td style="text-align: right; font-weight: 600; color: var(--accent);">
                                Rp {{ number_format($trx->total_amount, 0, ',', '.') }}
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

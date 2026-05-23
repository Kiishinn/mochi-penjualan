@extends('layouts.dashboard')

@section('title', 'Riwayat Transaksi Kasir')
@section('page-title', 'Riwayat Transaksi')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>Daftar Transaksi (Cabang Ini)</h3>
            <a href="{{ route('kasir.transactions.create') }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                Transaksi Baru
            </a>
        </div>

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>No Invoice</th>
                        <th>Total Item</th>
                        <th style="text-align: right;">Total Transaksi</th>
                        <th style="text-align: right;">Bayar</th>
                        <th style="text-align: right;">Kembalian</th>
                        <th style="width: 100px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr>
                            <td>{{ date('d/m/Y H:i', strtotime($sale->transaction_date)) }}</td>
                            <td>
                                <div style="font-weight: 500; color: var(--text-primary);">{{ $sale->invoice_number }}</div>
                            </td>
                            <td>{{ $sale->details->sum('quantity') }}</td>
                            <td style="text-align: right; font-weight: 600; color: var(--accent);">Rp {{ number_format($sale->total_price, 0, ',', '.') }}</td>
                            <td style="text-align: right;">Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</td>
                            <td style="text-align: right;">Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</td>
                            <td style="text-align: center;">
                                <a href="{{ route('kasir.transactions.show', $sale->id) }}" class="btn btn-secondary btn-sm" style="padding: 0.375rem 0.75rem;">Detail/Nota</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem;">Belum ada riwayat transaksi.</td>
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

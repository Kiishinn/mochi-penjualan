@extends('layouts.dashboard')

@section('title', 'Data Retur Barang')
@section('page-title', 'Data Retur Barang')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>Riwayat Retur Barang (Seluruh Cabang)</h3>
        </div>

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>No Invoice</th>
                        <th>Cabang</th>
                        <th>Produk</th>
                        <th style="text-align: center;">Jumlah</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returnItems as $return)
                        <tr>
                            <td>{{ date('d/m/Y', strtotime($return->return_date)) }}</td>
                            <td>
                                <div style="font-weight: 500; color: var(--text-primary);">{{ $return->sale->invoice_number ?? '-' }}</div>
                            </td>
                            <td>{{ $return->branch->name ?? '-' }}</td>
                            <td>{{ $return->product->name ?? '-' }}</td>
                            <td style="text-align: center;">{{ $return->quantity }}</td>
                            <td>
                                @if($return->status === 'pending')
                                    <span class="badge badge-warning">Menunggu</span>
                                @elseif($return->status === 'approved')
                                    <span class="badge badge-kasir">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem;">Belum ada riwayat retur barang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $returnItems->links() }}
            </div>
        </div>
    </div>
@endsection

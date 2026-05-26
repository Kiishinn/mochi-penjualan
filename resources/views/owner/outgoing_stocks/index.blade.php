@extends('layouts.dashboard')

@section('title', 'Data Stok Keluar')
@section('page-title', 'Data Stok Keluar')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>Riwayat Stok Keluar</h3>
        </div>

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>No. Referensi</th>
                        <th>Cabang</th>
                        <th>Alasan</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($outgoingStocks as $stock)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($stock->date)->format('d M Y H:i') }} WIB</td>
                            <td><div style="font-weight: 500; color: var(--text-primary);">{{ $stock->reference_number }}</div></td>
                            <td>{{ $stock->branch->name ?? '-' }}</td>
                            <td>
                                <span class="badge" style="background: rgba(245, 158, 11, 0.1); color: var(--warning);">
                                    {{ $stock->reason }}
                                </span>
                            </td>
                            <td>{{ $stock->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 2rem;">Belum ada data stok keluar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

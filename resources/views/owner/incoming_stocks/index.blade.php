@extends('layouts.dashboard')

@section('title', 'Data Stok Masuk')
@section('page-title', 'Data Stok Masuk')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>Riwayat Stok Masuk</h3>
        </div>

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>No. Referensi</th>
                        <th>Cabang</th>
                        <th>Supplier</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($incomingStocks as $stock)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($stock->date)->format('d M Y H:i') }} WIB</td>
                            <td><div style="font-weight: 500; color: var(--text-primary);">{{ $stock->reference_number }}</div></td>
                            <td>{{ $stock->branch->name ?? '-' }}</td>
                            <td>{{ $stock->supplier->name ?? '-' }}</td>
                            <td>{{ $stock->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 2rem;">Belum ada data stok masuk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

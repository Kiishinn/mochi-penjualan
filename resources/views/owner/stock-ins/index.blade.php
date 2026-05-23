@extends('layouts.dashboard')

@section('title', 'Data Stok Masuk')
@section('page-title', 'Data Stok Masuk')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>Riwayat Stok Masuk (Seluruh Cabang)</h3>
        </div>

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Cabang</th>
                        <th>Produk</th>
                        <th>Supplier</th>
                        <th style="text-align: center;">Jumlah</th>
                        <th>Keterangan</th>
                        <th>User</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stockIns as $in)
                        <tr>
                            <td>{{ date('d/m/Y', strtotime($in->date)) }}</td>
                            <td>{{ $in->branch->name ?? '-' }}</td>
                            <td>{{ $in->product->name ?? '-' }}</td>
                            <td>{{ $in->supplier->name ?? '-' }}</td>
                            <td style="text-align: center;">
                                <span class="badge badge-kasir">+{{ $in->quantity }}</span>
                            </td>
                            <td>{{ $in->note ?? '-' }}</td>
                            <td>{{ $in->creator->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem;">Belum ada riwayat stok masuk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $stockIns->links() }}
            </div>
        </div>
    </div>
@endsection

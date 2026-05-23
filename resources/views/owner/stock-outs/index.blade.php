@extends('layouts.dashboard')

@section('title', 'Data Stok Keluar')
@section('page-title', 'Data Stok Keluar')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>Riwayat Stok Keluar (Seluruh Cabang)</h3>
        </div>

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Cabang</th>
                        <th>Produk</th>
                        <th style="text-align: center;">Jumlah</th>
                        <th>Alasan</th>
                        <th>User</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stockOuts as $out)
                        <tr>
                            <td>{{ date('d/m/Y', strtotime($out->date)) }}</td>
                            <td>{{ $out->branch->name ?? '-' }}</td>
                            <td>{{ $out->product->name ?? '-' }}</td>
                            <td style="text-align: center;">
                                <span class="badge" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">-{{ $out->quantity }}</span>
                            </td>
                            <td>{{ $out->reason ?? '-' }}</td>
                            <td>{{ $out->creator->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem;">Belum ada riwayat stok keluar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $stockOuts->links() }}
            </div>
        </div>
    </div>
@endsection

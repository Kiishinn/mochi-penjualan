@extends('layouts.dashboard')

@section('title', 'Perpindahan Barang')
@section('page-title', 'Perpindahan Barang')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>Riwayat Perpindahan Barang Antar Cabang</h3>
        </div>

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Produk</th>
                        <th>Cabang Asal</th>
                        <th>Cabang Tujuan</th>
                        <th style="text-align: center;">Jumlah</th>
                        <th>Status</th>
                        <th>User</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transfers as $transfer)
                        <tr>
                            <td>{{ date('d/m/Y', strtotime($transfer->date)) }}</td>
                            <td>{{ $transfer->product->name ?? '-' }}</td>
                            <td>{{ $transfer->fromBranch->name ?? '-' }}</td>
                            <td>{{ $transfer->toBranch->name ?? '-' }}</td>
                            <td style="text-align: center;">{{ $transfer->quantity }}</td>
                            <td>
                                @if($transfer->status === 'pending')
                                    <span class="badge badge-warning">Menunggu</span>
                                @elseif($transfer->status === 'approved')
                                    <span class="badge badge-kasir">Disetujui</span>
                                @else
                                    <span class="badge" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">Ditolak</span>
                                @endif
                            </td>
                            <td>{{ $transfer->creator->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem;">Belum ada riwayat perpindahan barang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $transfers->links() }}
            </div>
        </div>
    </div>
@endsection

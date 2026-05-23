@extends('layouts.dashboard')

@section('title', 'Perpindahan Barang')
@section('page-title', 'Perpindahan Barang')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>Riwayat Perpindahan Barang</h3>
        </div>

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>No. Referensi</th>
                        <th>Cabang Asal</th>
                        <th>Cabang Tujuan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transfers as $transfer)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($transfer->date)->format('d M Y') }}</td>
                            <td><div style="font-weight: 500; color: var(--text-primary);">{{ $transfer->reference_number }}</div></td>
                            <td>{{ $transfer->sourceBranch->name ?? '-' }}</td>
                            <td>{{ $transfer->destinationBranch->name ?? '-' }}</td>
                            <td>
                                @if($transfer->status == 'completed')
                                    <span class="badge" style="background: rgba(34, 197, 94, 0.1); color: var(--success);">Selesai</span>
                                @elseif($transfer->status == 'pending')
                                    <span class="badge" style="background: rgba(245, 158, 11, 0.1); color: var(--warning);">Pending</span>
                                @else
                                    <span class="badge" style="background: rgba(239, 68, 68, 0.1); color: var(--danger);">{{ ucfirst($transfer->status) }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 2rem;">Belum ada data perpindahan barang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

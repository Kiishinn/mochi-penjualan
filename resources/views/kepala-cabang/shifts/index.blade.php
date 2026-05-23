@extends('layouts.dashboard')

@section('title', 'Manajemen Shift & Kas')
@section('page-title', 'Laporan Shift Kasir')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3 style="margin: 0; color: var(--text-primary);">Riwayat Buka/Tutup Kasir</h3>
    </div>

    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Kasir</th>
                    <th>Waktu Shift</th>
                    <th>Modal Awal</th>
                    <th>Pemasukan Sistem</th>
                    <th>Kas Fisik (Aktual)</th>
                    <th>Selisih</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($shifts as $shift)
                    @php
                        $isClosed = $shift->status === 'closed';
                        $discrepancy = $isClosed ? ($shift->ending_cash_actual - $shift->ending_cash_expected) : null;
                    @endphp
                    <tr>
                        <td style="font-weight: 500;">{{ $shift->user->name }}</td>
                        <td>
                            <div style="font-size: 0.85rem;">
                                <div style="color: var(--success);">Buka: {{ $shift->start_time->format('d M Y, H:i') }}</div>
                                @if($isClosed)
                                    <div style="color: var(--danger);">Tutup: {{ $shift->end_time->format('d M Y, H:i') }}</div>
                                @endif
                            </div>
                        </td>
                        <td>Rp {{ number_format($shift->starting_cash, 0, ',', '.') }}</td>
                        <td>
                            @if($isClosed)
                                Rp {{ number_format($shift->ending_cash_expected - $shift->starting_cash, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                        <td style="font-weight: 600;">
                            @if($isClosed)
                                Rp {{ number_format($shift->ending_cash_actual, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($isClosed)
                                @if($discrepancy == 0)
                                    <span style="color: var(--success); font-weight: 600;">Cocok</span>
                                @elseif($discrepancy > 0)
                                    <span style="color: var(--success); font-weight: 600;">+ Rp {{ number_format($discrepancy, 0, ',', '.') }}</span>
                                @else
                                    <span style="color: var(--danger); font-weight: 600;">- Rp {{ number_format(abs($discrepancy), 0, ',', '.') }}</span>
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($shift->status === 'open')
                                <span class="badge" style="background: rgba(34, 197, 94, 0.1); color: var(--success);">Aktif</span>
                            @else
                                <span class="badge" style="background: rgba(100, 116, 139, 0.1); color: var(--text-muted);">Selesai</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                            Belum ada catatan shift.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div style="margin-top: 1rem;">
        {{ $shifts->links() }}
    </div>
</div>
@endsection

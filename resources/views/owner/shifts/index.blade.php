@extends('layouts.dashboard')

@section('title', 'Manajemen Shift & Kas')
@section('page-title', 'Laporan Shift Kasir (Global)')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>Riwayat Buka/Tutup Kasir Seluruh Cabang</h3>
        <div>
            <form id="filterForm" method="GET" action="{{ route('shifts.index') }}" style="display: flex; gap: 0.5rem; width: 100%;">
                <select name="branch_id" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Cabang</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
                <input type="text" id="searchInput" name="search" class="form-control" placeholder="Cari nama kasir..." value="{{ request('search') }}" autocomplete="off">
                @if(request()->hasAny(['search', 'branch_id']) && (request('search') != '' || request('branch_id') != ''))
                    <a href="{{ route('shifts.index') }}" class="btn btn-secondary btn-sm" style="background: transparent; color: var(--danger); border-color: var(--danger);">Reset</a>
                @endif
            </form>
        </div>
    </div>

    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Kasir</th>
                    <th>Cabang</th>
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
                        <td style="font-weight: 500;">{{ $shift->user->name ?? '-' }}</td>
                        <td>{{ $shift->branch->name ?? '-' }}</td>
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
                        <td colspan="8" style="text-align: center; padding: 2rem; color: var(--text-muted);">
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let searchTimeout;
        const searchInput = document.getElementById('searchInput');
        const filterForm = document.getElementById('filterForm');

        if(searchInput) {
            if (searchInput.value.length > 0) {
                const length = searchInput.value.length;
                if (window.innerWidth > 768) searchInput.focus();
                searchInput.setSelectionRange(length, length);
            }

                        searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    filterForm.submit();
                }, 800);
            });
            
                        window.addEventListener('beforeunload', function() {
                clearTimeout(searchTimeout);
            });
            document.body.addEventListener('click', function(e) {
                if (e.target.closest('a') || e.target.closest('button')) {
                    clearTimeout(searchTimeout);
                }
            });}
    });
</script>
@endsection




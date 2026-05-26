@extends('layouts.dashboard')

@section('title', 'Kartu Stok')
@section('page-title', 'Kartu Stok (Riwayat Barang)')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">{{ $product->name }}</h2>
            <div style="color: var(--text-muted); display: flex; gap: 1rem; align-items: center;">
                <span><i class="fas fa-tags"></i> {{ $product->category->name ?? '-' }}</span>
                <span><i class="fas fa-store"></i> Cabang: <strong>{{ Auth::user()->branch->name ?? '' }}</strong></span>
            </div>
        </div>
        <div>
            <a href="{{ url()->previous() }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Kronologi Pergerakan Stok</h3>
        </div>
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 180px;">Waktu Kejadian</th>
                        <th>Tipe Transaksi</th>
                        <th>Keterangan / Referensi</th>
                        <th style="text-align: right; width: 100px;">Masuk</th>
                        <th style="text-align: right; width: 100px;">Keluar</th>
                        <th style="text-align: right; width: 100px;">Sisa Stok</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sortedEvents as $event)
                        <tr>
                            <td>
                                <div>{{ \Carbon\Carbon::parse($event['date'])->format('d M Y') }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">{{ \Carbon\Carbon::parse($event['date'])->format('H:i:s') }}</div>
                            </td>
                            <td>
                                @if($event['qty'] > 0)
                                    <span class="badge" style="background: rgba(34, 197, 94, 0.1); color: var(--success);">{{ $event['type'] }}</span>
                                @else
                                    <span class="badge" style="background: rgba(239, 68, 68, 0.1); color: var(--danger);">{{ $event['type'] }}</span>
                                @endif
                            </td>
                            <td><span style="font-size: 0.9rem;">{{ $event['ref'] }}</span></td>
                            <td style="text-align: right;">
                                @if($event['qty'] > 0)
                                    <span style="color: var(--success); font-weight: 600;">+{{ $event['qty'] }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td style="text-align: right;">
                                @if($event['qty'] < 0)
                                    <span style="color: var(--danger); font-weight: 600;">{{ $event['qty'] }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td style="text-align: right; font-weight: 700; background: rgba(0,0,0,0.02);">
                                {{ $event['balance'] }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem;">Belum ada riwayat pergerakan untuk barang ini di cabang tersebut.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

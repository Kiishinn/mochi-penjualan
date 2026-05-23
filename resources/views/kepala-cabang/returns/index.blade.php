@extends('layouts.dashboard')

@section('title', 'Data Retur Barang')
@section('page-title', 'Data Retur Barang')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>Riwayat Pengajuan Retur Barang (Dari Pelanggan)</h3>
            <a href="{{ route('kepala-cabang.returns.create') }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                Buat Pengajuan Retur
            </a>
        </div>

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>No Invoice</th>
                        <th>Produk</th>
                        <th style="text-align: center;">Jumlah</th>
                        <th>Alasan</th>
                        <th>Status</th>
                        <th style="width: 100px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returnItems as $return)
                        <tr>
                            <td>{{ date('d/m/Y', strtotime($return->return_date)) }}</td>
                            <td>
                                <div style="font-weight: 500; color: var(--text-primary);">{{ $return->sale->invoice_number ?? '-' }}</div>
                            </td>
                            <td>{{ $return->product->name ?? '-' }}</td>
                            <td style="text-align: center;">{{ $return->quantity }}</td>
                            <td>{{ $return->reason ?? '-' }}</td>
                            <td>
                                @if($return->status === 'pending')
                                    <span class="badge badge-warning">Menunggu Diproses</span>
                                @elseif($return->status === 'approved')
                                    <span class="badge badge-kasir">Disetujui (Stok Kembali)</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <a href="{{ route('kepala-cabang.returns.show', $return->id) }}" class="btn btn-secondary btn-sm" style="padding: 0.375rem 0.75rem;">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem;">Belum ada riwayat retur barang.</td>
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

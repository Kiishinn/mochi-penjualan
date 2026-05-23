@extends('layouts.dashboard')

@section('title', 'Data Stok Keluar')
@section('page-title', 'Data Stok Keluar')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>Riwayat Stok Keluar (Cabang Ini)</h3>
            <a href="{{ route('kepala-cabang.stock-outs.create') }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                Tambah Stok Keluar
            </a>
        </div>

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Produk</th>
                        <th style="text-align: center;">Jumlah</th>
                        <th>Alasan</th>
                        <th style="width: 100px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stockOuts as $out)
                        <tr>
                            <td>{{ date('d/m/Y', strtotime($out->date)) }}</td>
                            <td>{{ $out->product->name ?? '-' }}</td>
                            <td style="text-align: center;">
                                <span class="badge" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">-{{ $out->quantity }}</span>
                            </td>
                            <td>{{ $out->reason ?? '-' }}</td>
                            <td style="text-align: center;">
                                <a href="{{ route('kepala-cabang.stock-outs.edit', $out->id) }}" class="btn btn-secondary btn-sm" style="padding: 0.375rem 0.75rem;">Edit Alasan</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 2rem;">Belum ada riwayat stok keluar.</td>
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

@extends('layouts.dashboard')

@section('title', 'Data Stok Masuk')
@section('page-title', 'Data Stok Masuk')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>Riwayat Stok Masuk (Cabang Ini)</h3>
            <a href="{{ route('kepala-cabang.stock-ins.create') }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                Tambah Stok Masuk
            </a>
        </div>

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Produk</th>
                        <th>Supplier</th>
                        <th style="text-align: center;">Jumlah</th>
                        <th>Keterangan</th>
                        <th style="width: 100px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stockIns as $in)
                        <tr>
                            <td>{{ date('d/m/Y', strtotime($in->date)) }}</td>
                            <td>{{ $in->product->name ?? '-' }}</td>
                            <td>{{ $in->supplier->name ?? '-' }}</td>
                            <td style="text-align: center;">
                                <span class="badge badge-kasir">+{{ $in->quantity }}</span>
                            </td>
                            <td>{{ $in->note ?? '-' }}</td>
                            <td style="text-align: center;">
                                <a href="{{ route('kepala-cabang.stock-ins.edit', $in->id) }}" class="btn btn-secondary btn-sm" style="padding: 0.375rem 0.75rem;">Edit Keterangan</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem;">Belum ada riwayat stok masuk.</td>
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

@extends('layouts.dashboard')

@section('title', 'Detail Retur')
@section('page-title', 'Detail Retur')

@section('content')
    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <div class="card-header">
            <h3>Detail Pengajuan Retur</h3>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <table style="width: 100%; border-spacing: 0; border-collapse: collapse;">
                <tr>
                    <td style="padding: 0.5rem 0; font-weight: 500; color: var(--text-muted); width: 150px;">Status</td>
                    <td style="padding: 0.5rem 0;">
                        @if($return->status === 'pending')
                            <span class="badge badge-warning">Menunggu Diproses</span>
                        @else
                            <span class="badge badge-kasir">Disetujui</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0.5rem 0; font-weight: 500; color: var(--text-muted);">No Invoice</td>
                    <td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-primary);">{{ $return->sale->invoice_number ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 0.5rem 0; font-weight: 500; color: var(--text-muted);">Tanggal Transaksi</td>
                    <td style="padding: 0.5rem 0;">{{ isset($return->sale) ? date('d/m/Y', strtotime($return->sale->transaction_date)) : '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 0.5rem 0; font-weight: 500; color: var(--text-muted);">Tanggal Retur</td>
                    <td style="padding: 0.5rem 0;">{{ date('d/m/Y', strtotime($return->return_date)) }}</td>
                </tr>
                <tr>
                    <td style="padding: 0.5rem 0; font-weight: 500; color: var(--text-muted);">Produk</td>
                    <td style="padding: 0.5rem 0;">{{ $return->product->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 0.5rem 0; font-weight: 500; color: var(--text-muted);">Jumlah</td>
                    <td style="padding: 0.5rem 0; font-weight: 600; color: #ef4444;">{{ $return->quantity }}</td>
                </tr>
                <tr>
                    <td style="padding: 0.5rem 0; font-weight: 500; color: var(--text-muted);">Alasan</td>
                    <td style="padding: 0.5rem 0;">{{ $return->reason ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 0.5rem 0; font-weight: 500; color: var(--text-muted);">Diajukan Oleh</td>
                    <td style="padding: 0.5rem 0;">{{ $return->user->name ?? '-' }}</td>
                </tr>
            </table>
        </div>

        @if($return->status === 'pending')
            <form action="{{ route('kepala-cabang.returns.update', $return->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="approved">
                
                <div class="alert alert-warning" style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); color: #f59e0b;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    Menyetujui retur akan secara otomatis mengembalikan stok produk ke gudang cabang. Pastikan barang fisik sudah diterima.
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" style="background: var(--success);">Setujui & Kembalikan Stok</button>
                    <a href="{{ route('kepala-cabang.returns.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        @else
            <div class="form-actions">
                <a href="{{ route('kepala-cabang.returns.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        @endif
    </div>
@endsection

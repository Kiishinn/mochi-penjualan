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
                        @elseif($return->status === 'approved')
                            <span class="badge badge-kasir">Disetujui</span>
                        @elseif($return->status === 'rejected')
                            <span class="badge" style="background: rgba(239, 68, 68, 0.1); color: var(--danger);">Ditolak</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0.5rem 0; font-weight: 500; color: var(--text-muted);">No Invoice</td>
                    <td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-primary);">{{ $return->sale->invoice_number ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 0.5rem 0; font-weight: 500; color: var(--text-muted);">Tanggal Transaksi</td>
                    <td style="padding: 0.5rem 0;">{{ isset($return->sale) ? date('d/m/Y H:i', strtotime($return->sale->transaction_date)) : '-' }} WIB</td>
                </tr>
                <tr>
                    <td style="padding: 0.5rem 0; font-weight: 500; color: var(--text-muted);">Tanggal Retur</td>
                    <td style="padding: 0.5rem 0;">{{ $return->created_at->format('d/m/Y H:i') }} WIB</td>
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
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <!-- Form Setuju -->
                <form action="{{ route('kepala-cabang.returns.update', $return->id) }}" method="POST" style="flex: 1; min-width: 300px;">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="approved">
                    
                    <div class="alert alert-warning" style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); color: #f59e0b; margin-bottom: 1rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        Menyetujui retur akan secara otomatis menyesuaikan stok dan membatalkan pendapatan kasir. Pastikan fisik barang sudah sesuai.
                    </div>
                    <button type="submit" class="btn btn-primary" style="background: var(--success); width: 100%; margin-bottom: 0.5rem;" onclick="return confirm('Yakin ingin menyetujui retur ini?')">Setujui & Selesaikan</button>
                </form>

                <!-- Form Tolak -->
                <form action="{{ route('kepala-cabang.returns.update', $return->id) }}" method="POST" style="flex: 1; min-width: 300px;">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="rejected">
                    
                    <div class="alert alert-danger" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: var(--danger); margin-bottom: 1rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Tolak retur jika terjadi human error oleh kasir atau tidak memenuhi syarat retur. Stok & uang tidak akan berubah.
                    </div>
                    <button type="submit" class="btn btn-secondary" style="width: 100%; background: transparent; border-color: var(--danger); color: var(--danger); margin-bottom: 0.5rem;" onclick="return confirm('Yakin ingin MENOLAK pengajuan retur ini?')">Tolak & Batalkan</button>
                </form>
            </div>
            
            <div class="form-actions" style="margin-top: 1rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                <a href="{{ route('kepala-cabang.returns.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        @else
            <div class="form-actions">
                <a href="{{ route('kepala-cabang.returns.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        @endif
    </div>
@endsection

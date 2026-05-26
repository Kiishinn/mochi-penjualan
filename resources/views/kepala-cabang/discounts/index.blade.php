@extends('layouts.dashboard')

@section('title', 'Pengajuan Diskon Produk')
@section('page-title', 'Pengajuan Diskon Produk')

@section('content')
    <div class="card" style="margin-bottom: 1.5rem;">
        <div class="card-header" style="justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <h3 style="margin: 0;">Riwayat Pengajuan Diskon</h3>
                <a href="{{ route('kepala-cabang.discounts.create') }}" class="btn btn-primary btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Ajukan Diskon Baru
                </a>
            </div>
        </div>
        
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Nilai Diskon</th>
                        <th>Periode Aktif</th>
                        <th>Status</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($discounts as $discount)
                        <tr>
                            <td>
                                <div style="font-weight: 500; color: var(--text-primary);">{{ $discount->product->name }}</div>
                            </td>
                            <td>
                                @if($discount->discount_type == 'percentage')
                                    <span class="badge badge-success">{{ number_format($discount->discount_value, 0) }}%</span>
                                @else
                                    <span class="badge badge-success">Rp {{ number_format($discount->discount_value, 0, ',', '.') }}</span>
                                @endif
                            </td>
                            <td>
                                <div style="font-size: 0.875rem;">
                                    {{ $discount->start_date->format('d M Y H:i') }} <br> s/d <br> {{ $discount->end_date->format('d M Y H:i') }}
                                </div>
                            </td>
                            <td>
                                @if($discount->status == 'pending')
                                    <span class="badge badge-warning">Menunggu</span>
                                @elseif($discount->status == 'approved')
                                    <span class="badge badge-success">Disetujui</span>
                                @else
                                    <span class="badge badge-danger">Ditolak/Berhenti</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @if($discount->status == 'pending')
                                    <form action="{{ route('kepala-cabang.discounts.destroy', $discount->id) }}" method="POST" class="form-delete" style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-secondary btn-sm" style="color: var(--danger); border-color: var(--danger); background: transparent;">Batalkan</button>
                                    </form>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 2rem;">Belum ada pengajuan diskon.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
            <div class="mt-4" style="padding: 1rem;">
                {{ $discounts->links() }}
            </div>
        </div>
    </div>
@endsection

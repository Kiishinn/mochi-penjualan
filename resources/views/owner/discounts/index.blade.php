@extends('layouts.dashboard')

@section('title', 'Persetujuan Diskon')
@section('page-title', 'Persetujuan Diskon Produk')

@section('content')
    <div class="card" style="margin-bottom: 1.5rem;">
        <div class="card-header">
            <h3>Daftar Pengajuan Diskon</h3>
        </div>
        
        <form method="GET" action="{{ route('owner.discounts.index') }}" class="form-grid" style="align-items: flex-end; padding: 1rem 1.5rem; border-bottom: 1px solid var(--border-color);">
            <div class="form-group" style="margin-bottom: 0;">
                <label>Status</label>
                <select name="status" class="form-control" onchange="this.form.submit()">
                    <option value="pending" {{ request('status') == 'pending' || !request()->has('status') ? 'selected' : '' }}>Menunggu Persetujuan</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak / Diberhentikan</option>
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label>Cabang</label>
                <select name="branch_id" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Cabang</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            
            @if(request()->has('status') || request()->has('branch_id'))
                <a href="{{ route('owner.discounts.index') }}" class="btn btn-secondary" style="height: 38px; display: inline-flex; align-items: center;">Reset Filter</a>
            @endif
        </form>
        
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Cabang</th>
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
                            <td>{{ $discount->branch->name }}</td>
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
                                    <div style="display: flex; gap: 0.25rem; justify-content: center;">
                                        <form action="{{ route('owner.discounts.approve', $discount->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-primary btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Setujui</button>
                                        </form>
                                        
                                        <form action="{{ route('owner.discounts.reject', $discount->id) }}" method="POST" class="form-delete" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-secondary btn-sm" style="color: var(--danger); border-color: var(--danger); background: transparent; padding: 0.25rem 0.5rem; font-size: 0.75rem;">Tolak</button>
                                        </form>
                                    </div>
                                @elseif($discount->status == 'approved')
                                    <form action="{{ route('owner.discounts.reject', $discount->id) }}" method="POST" class="form-delete" style="display: inline-block;">
                                        @csrf
                                        <button type="submit" class="btn btn-secondary btn-sm" style="color: var(--danger); border-color: var(--danger); background: transparent; padding: 0.25rem 0.5rem; font-size: 0.75rem;">Berhentikan Paksa</button>
                                    </form>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem;">Belum ada pengajuan diskon yang {{ request('status', 'pending') }}.</td>
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

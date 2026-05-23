@extends('layouts.dashboard')

@section('title', 'Data Cabang')
@section('page-title', 'Data Cabang')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>Daftar Cabang</h3>
            <a href="{{ route('branches.create') }}" class="btn btn-primary btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Cabang
            </a>
        </div>

        @if(session('error'))
            <div class="alert" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #f87171;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama Cabang</th>
                        <th>Alamat & Keterangan</th>
                        <th>No. Telepon</th>
                        <th>Status</th>
                        <th>Total User</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($branches as $branch)
                        <tr>
                            <td>
                                <div style="font-weight: 500; color: var(--text-primary);">{{ $branch->name }}</div>
                            </td>
                            <td>
                                <div>{{ $branch->address ?? '-' }}</div>
                                @if($branch->description)
                                    <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">
                                        <i>{{ $branch->description }}</i>
                                    </div>
                                @endif
                            </td>
                            <td>{{ $branch->phone ?? '-' }}</td>
                            <td>
                                @if($branch->is_active)
                                    <span class="badge badge-kasir">Beroperasi</span>
                                @else
                                    <span class="badge badge-warning">Tutup / Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge" style="background: rgba(14, 165, 233, 0.1); color: #0ea5e9;">{{ $branch->users_count }} User</span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                    <a href="{{ route('branches.edit', $branch->id) }}" class="btn btn-secondary btn-sm" title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.89 1.112l-2.951.821c-.34.095-.664-.23-.569-.569l.821-2.951a4.5 4.5 0 0 1 1.112-1.89l13.438-13.438Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 7.125 16.875 4.5" />
                                        </svg>
                                        Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem;">Belum ada data cabang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $branches->links() }}
            </div>
        </div>
    </div>
@endsection

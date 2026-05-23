@extends('layouts.dashboard')

@section('title', 'Data User')
@section('page-title', 'Data User')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>Daftar Pengguna</h3>
            <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah User
            </a>
        </div>

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email / Telp</th>
                        <th>Role</th>
                        <th>Cabang</th>
                        <th>Status</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div style="font-weight: 500; color: var(--text-primary);">{{ $user->name }}</div>
                            </td>
                            <td>
                                <div>{{ $user->email }}</div>
                                @if($user->phone)
                                    <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">
                                        📞 {{ $user->phone }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $user->role }}">
                                    {{ str_replace('_', ' ', $user->role) }}
                                </span>
                            </td>
                            <td>{{ $user->branch->name ?? '-' }}</td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge badge-kasir">Aktif</span>
                                @else
                                    <span class="badge badge-warning">Nonaktif</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-secondary btn-sm" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.89 1.112l-2.951.821c-.34.095-.664-.23-.569-.569l.821-2.951a4.5 4.5 0 0 1 1.112-1.89l13.438-13.438Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 7.125 16.875 4.5" />
                                    </svg>
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem;">Belum ada data user.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
@endsection

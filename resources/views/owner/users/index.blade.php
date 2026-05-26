@extends('layouts.dashboard')

@section('title', 'Data User')
@section('page-title', 'Data User')

@section('content')
    <div class="card">
        <div class="card-header" style="flex-wrap: wrap; gap: 1rem;">
            <h3>Daftar Pengguna</h3>
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center; margin-left: auto;">
                <form id="filterForm" method="GET" action="{{ route('users.index') }}" style="display: flex; gap: 0.5rem;">
                    <select name="branch_id" class="form-control" style="width: auto; padding: 0.25rem 2rem 0.25rem 0.5rem;" onchange="this.form.submit()">
                        <option value="">Semua Cabang</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                    <input type="text" id="searchInput" name="search" class="form-control" placeholder="Cari nama/username..." value="{{ request('search') }}" style="width: 200px; padding: 0.25rem 0.5rem;" autocomplete="off">
                    
                    @if(request()->hasAny(['search', 'branch_id']) && (request('search') != '' || request('branch_id') != ''))
                        <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm" style="background: transparent; color: var(--danger); border-color: var(--danger);">Reset</a>
                    @endif
                </form>
                <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Tambah User
                </a>
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Username</th>
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
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    @if($user->photo_profile)
                                        <img src="{{ Storage::url($user->photo_profile) }}" alt="Avatar" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover;">
                                    @else
                                        <div style="width: 36px; height: 36px; border-radius: 50%; background: var(--bg-input); display: flex; align-items: center; justify-content: center; font-weight: 600; color: var(--text-secondary);">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div style="font-weight: 500; color: var(--text-primary);">{{ $user->name }}</div>
                                        @if($user->photo_ktp)
                                            <span style="font-size: 0.7rem; background: rgba(34, 197, 94, 0.1); color: var(--success); padding: 0.1rem 0.4rem; border-radius: 12px; margin-top: 2px; display: inline-block;">KTP Terarsip</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="color: var(--text-secondary);">{{ $user->username ?? '-' }}</div>
                            </td>
                            <td>
                                <div>{{ $user->email }}</div>
                                @if($user->phone)
                                    <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">
                                        {{ $user->phone }}
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let searchTimeout;
            const searchInput = document.getElementById('searchInput');
            const filterForm = document.getElementById('filterForm');

            if(searchInput) {
                // Focus and move cursor to end
                if (searchInput.value.length > 0) {
                    const length = searchInput.value.length;
                    if (window.innerWidth > 768) searchInput.focus();
                    searchInput.setSelectionRange(length, length);
                }

                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(function() {
                        filterForm.submit();
                    }, 800); // 800ms debounce
                });
            }
        });
    </script>
@endsection




@extends('layouts.dashboard')

@section('title', 'Data Satuan')
@section('page-title', 'Data Satuan')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>Daftar Satuan</h3>
            <a href="{{ route('kepala-cabang.units.create') }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                Tambah Satuan
            </a>
        </div>

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Satuan</th>
                        <th style="width: 150px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($units as $index => $unit)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><div style="font-weight: 500; color: var(--text-primary);">{{ $unit->name }}</div></td>
                            <td style="text-align: center;">
                                <a href="{{ route('kepala-cabang.units.edit', $unit->id) }}" class="btn btn-secondary btn-sm" style="padding: 0.375rem 0.75rem;">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 2rem;">Belum ada data satuan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $units->links() }}
            </div>
        </div>
    </div>
@endsection

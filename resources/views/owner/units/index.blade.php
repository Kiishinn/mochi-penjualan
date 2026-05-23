@extends('layouts.dashboard')

@section('title', 'Data Satuan')
@section('page-title', 'Data Satuan')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>Daftar Satuan</h3>
        </div>

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Satuan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($units as $index => $unit)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><div style="font-weight: 500; color: var(--text-primary);">{{ $unit->name }}</div></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" style="text-align: center; padding: 2rem;">Belum ada data satuan.</td>
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

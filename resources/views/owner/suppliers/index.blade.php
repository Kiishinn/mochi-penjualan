@extends('layouts.dashboard')

@section('title', 'Data Supplier')
@section('page-title', 'Data Supplier')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>Daftar Supplier</h3>
        </div>

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Supplier</th>
                        <th>No HP / Telepon</th>
                        <th>Alamat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $index => $supplier)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><div style="font-weight: 500; color: var(--text-primary);">{{ $supplier->name }}</div></td>
                            <td>{{ $supplier->phone ?? '-' }}</td>
                            <td>{{ $supplier->address ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 2rem;">Belum ada data supplier.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $suppliers->links() }}
            </div>
        </div>
    </div>
@endsection

@extends('layouts.dashboard')

@section('title', 'Data Supplier')
@section('page-title', 'Data Supplier')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>Daftar Supplier</h3>
            <a href="{{ route('kepala-cabang.suppliers.create') }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                Tambah Supplier
            </a>
        </div>

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Supplier</th>
                        <th>No HP / Telepon</th>
                        <th>Alamat</th>
                        <th style="width: 150px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $index => $supplier)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><div style="font-weight: 500; color: var(--text-primary);">{{ $supplier->name }}</div></td>
                            <td>{{ $supplier->phone ?? '-' }}</td>
                            <td>{{ $supplier->address ?? '-' }}</td>
                            <td style="text-align: center;">
                                <a href="{{ route('kepala-cabang.suppliers.edit', $supplier->id) }}" class="btn btn-secondary btn-sm" style="padding: 0.375rem 0.75rem;">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 2rem;">Belum ada data supplier.</td>
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

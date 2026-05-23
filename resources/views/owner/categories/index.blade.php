@extends('layouts.dashboard')

@section('title', 'Data Kategori')
@section('page-title', 'Data Kategori')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>Daftar Kategori</h3>
        </div>

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Kategori</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $index => $category)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><div style="font-weight: 500; color: var(--text-primary);">{{ $category->name }}</div></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" style="text-align: center; padding: 2rem;">Belum ada data kategori.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $categories->links() }}
            </div>
        </div>
    </div>
@endsection

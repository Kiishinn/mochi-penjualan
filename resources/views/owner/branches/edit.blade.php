@extends('layouts.dashboard')

@section('title', 'Edit Cabang')
@section('page-title', 'Edit Cabang: ' . $branch->name)

@section('content')
    <div class="card" style="max-width: 800px;">
        <div class="card-header" style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem;">
            <h3>Informasi Cabang</h3>
            <a href="{{ route('branches.index') }}" class="btn btn-secondary btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                </svg>
                Kembali
            </a>
        </div>

        <form method="POST" action="{{ route('branches.update', $branch->id) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Nama Cabang <span style="color: var(--danger);">*</span></label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $branch->name) }}" required>
                @error('name')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="address">Alamat Lengkap</label>
                <textarea id="address" name="address" class="form-control" rows="3">{{ old('address', $branch->address) }}</textarea>
                @error('address')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Catatan / Deskripsi Tambahan</label>
                <textarea id="description" name="description" class="form-control" rows="2">{{ old('description', $branch->description) }}</textarea>
                @error('description')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status Aktif -->
            <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem;">
                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $branch->is_active) ? 'checked' : '' }} style="width: 1rem; height: 1rem; accent-color: var(--primary);">
                <label for="is_active" style="margin-bottom: 0; cursor: pointer;">Cabang Aktif / Beroperasi</label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                    </svg>
                    Perbarui Cabang
                </button>
            </div>
        </form>
    </div>
@endsection

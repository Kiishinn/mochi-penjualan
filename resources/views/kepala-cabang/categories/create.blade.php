@extends('layouts.dashboard')

@section('title', 'Tambah Kategori')
@section('page-title', 'Tambah Kategori')

@section('content')
    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <div class="card-header">
            <h3>Form Tambah Kategori</h3>
        </div>

        <form action="{{ route('kepala-cabang.categories.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Nama Kategori</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required autofocus>
                @error('name') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan Kategori</button>
                <a href="{{ route('kepala-cabang.categories.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection

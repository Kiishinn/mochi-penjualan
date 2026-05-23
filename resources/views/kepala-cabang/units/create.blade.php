@extends('layouts.dashboard')

@section('title', 'Tambah Satuan')
@section('page-title', 'Tambah Satuan')

@section('content')
    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <div class="card-header">
            <h3>Form Tambah Satuan</h3>
        </div>

        <form action="{{ route('kepala-cabang.units.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Nama Satuan (contoh: pcs, kg, pack)</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required autofocus>
                @error('name') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan Satuan</button>
                <a href="{{ route('kepala-cabang.units.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection

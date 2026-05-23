@extends('layouts.dashboard')

@section('title', 'Edit Satuan')
@section('page-title', 'Edit Satuan')

@section('content')
    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <div class="card-header">
            <h3>Form Edit Satuan</h3>
        </div>

        <form action="{{ route('kepala-cabang.units.update', $unit->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="name">Nama Satuan</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $unit->name) }}" required autofocus>
                @error('name') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('kepala-cabang.units.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection

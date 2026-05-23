@extends('layouts.dashboard')

@section('title', 'Tambah Supplier')
@section('page-title', 'Tambah Supplier')

@section('content')
    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <div class="card-header">
            <h3>Form Tambah Supplier</h3>
        </div>

        <form action="{{ route('kepala-cabang.suppliers.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Nama Supplier</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required autofocus>
                @error('name') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            
            <div class="form-group">
                <label for="phone">No HP / Telepon</label>
                <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone') }}">
                @error('phone') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            
            <div class="form-group">
                <label for="address">Alamat Lengkap</label>
                <textarea id="address" name="address" class="form-control" rows="3">{{ old('address') }}</textarea>
                @error('address') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan Supplier</button>
                <a href="{{ route('kepala-cabang.suppliers.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection

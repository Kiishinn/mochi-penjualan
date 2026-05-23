@extends('layouts.dashboard')

@section('title', 'Edit Stok Masuk')
@section('page-title', 'Edit Stok Masuk')

@section('content')
    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <div class="card-header">
            <h3>Edit Keterangan Stok Masuk</h3>
        </div>

        <form action="{{ route('kepala-cabang.stock-ins.update', $stock_in->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label>Tanggal</label>
                <input type="text" class="form-control" value="{{ date('d/m/Y', strtotime($stock_in->date)) }}" disabled>
            </div>

            <div class="form-group">
                <label>Produk</label>
                <input type="text" class="form-control" value="{{ $stock_in->product->name ?? '-' }}" disabled>
            </div>

            <div class="form-group">
                <label>Jumlah</label>
                <input type="text" class="form-control" value="{{ $stock_in->quantity }}" disabled>
            </div>

            <div class="form-group">
                <label for="note">Keterangan</label>
                <textarea id="note" name="note" class="form-control" rows="3" autofocus>{{ old('note', $stock_in->note) }}</textarea>
                @error('note') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('kepala-cabang.stock-ins.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection

@extends('layouts.dashboard')

@section('title', 'Edit Stok Keluar')
@section('page-title', 'Edit Stok Keluar')

@section('content')
    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <div class="card-header">
            <h3>Edit Alasan Stok Keluar</h3>
        </div>

        <form action="{{ route('kepala-cabang.stock-outs.update', $stock_out->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label>Tanggal</label>
                <input type="text" class="form-control" value="{{ date('d/m/Y', strtotime($stock_out->date)) }}" disabled>
            </div>

            <div class="form-group">
                <label>Produk</label>
                <input type="text" class="form-control" value="{{ $stock_out->product->name ?? '-' }}" disabled>
            </div>

            <div class="form-group">
                <label>Jumlah</label>
                <input type="text" class="form-control" value="{{ $stock_out->quantity }}" disabled>
            </div>

            <div class="form-group">
                <label for="reason">Alasan</label>
                <textarea id="reason" name="reason" class="form-control" rows="3" autofocus>{{ old('reason', $stock_out->reason) }}</textarea>
                @error('reason') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('kepala-cabang.stock-outs.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection

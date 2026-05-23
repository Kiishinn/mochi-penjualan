@extends('layouts.dashboard')

@section('title', 'Tambah Stok Masuk')
@section('page-title', 'Tambah Stok Masuk')

@section('content')
    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <div class="card-header">
            <h3>Form Stok Masuk</h3>
        </div>

        <form action="{{ route('kepala-cabang.stock-ins.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="date">Tanggal Masuk</label>
                <input type="date" id="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
                @error('date') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="product_id">Produk <span style="color: #ef4444;">*</span></label>
                <select id="product_id" name="product_id" class="form-control" required>
                    <option value="">Pilih Produk</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" @if(old('product_id') == $product->id) selected @endif>{{ $product->name }}</option>
                    @endforeach
                </select>
                @error('product_id') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="supplier_id">Supplier (Opsional)</label>
                <select id="supplier_id" name="supplier_id" class="form-control">
                    <option value="">Pilih Supplier</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" @if(old('supplier_id') == $supplier->id) selected @endif>{{ $supplier->name }}</option>
                    @endforeach
                </select>
                @error('supplier_id') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="quantity">Jumlah Masuk <span style="color: #ef4444;">*</span></label>
                <input type="number" id="quantity" name="quantity" class="form-control" value="{{ old('quantity') }}" required min="1">
                @error('quantity') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="note">Keterangan</label>
                <textarea id="note" name="note" class="form-control" rows="3">{{ old('note') }}</textarea>
                @error('note') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan Stok Masuk</button>
                <a href="{{ route('kepala-cabang.stock-ins.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection

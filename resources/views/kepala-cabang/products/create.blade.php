@extends('layouts.dashboard')

@section('title', 'Tambah Produk')
@section('page-title', 'Tambah Produk')

@section('content')
    <div class="card" style="max-width: 800px; margin: 0 auto;">
        <div class="card-header">
            <h3>Form Tambah Produk</h3>
        </div>

        <form action="{{ route('kepala-cabang.products.store') }}" method="POST">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label for="barcode">Barcode</label>
                    <input type="text" id="barcode" name="barcode" class="form-control" value="{{ old('barcode') }}" autofocus>
                    @error('barcode') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                
                <div class="form-group">
                    <label for="name">Nama Produk <span style="color: #ef4444;">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
                    @error('name') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="category_id">Kategori <span style="color: #ef4444;">*</span></label>
                    <select id="category_id" name="category_id" class="form-control" required>
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @if(old('category_id') == $category->id) selected @endif>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="unit_id">Satuan <span style="color: #ef4444;">*</span></label>
                    <select id="unit_id" name="unit_id" class="form-control" required>
                        <option value="">Pilih Satuan</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}" @if(old('unit_id') == $unit->id) selected @endif>{{ $unit->name }}</option>
                        @endforeach
                    </select>
                    @error('unit_id') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="purchase_price">Harga Beli <span style="color: #ef4444;">*</span></label>
                    <input type="number" id="purchase_price" name="purchase_price" class="form-control" value="{{ old('purchase_price') }}" required min="0">
                    @error('purchase_price') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="selling_price">Harga Jual <span style="color: #ef4444;">*</span></label>
                    <input type="number" id="selling_price" name="selling_price" class="form-control" value="{{ old('selling_price') }}" required min="0">
                    @error('selling_price') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="minimum_stock">Minimal Stok</label>
                    <input type="number" id="minimum_stock" name="minimum_stock" class="form-control" value="{{ old('minimum_stock', 5) }}" min="0">
                    @error('minimum_stock') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-group" style="margin-top: 1rem;">
                <label for="description">Deskripsi</label>
                <textarea id="description" name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                @error('description') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan Produk</button>
                <a href="{{ route('kepala-cabang.products.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection

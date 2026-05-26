@extends('layouts.dashboard')

@section('title', 'Edit Produk')
@section('page-title', 'Edit Produk')

@section('content')
    <div class="card" style="max-width: 800px; margin: 0 auto;">
        <div class="card-header">
            <h3>Form Edit Produk</h3>
        </div>

        <form action="{{ route('kepala-cabang.products.update', $product->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-grid">
                
                <div class="form-group">
                    <label for="name">Nama Produk <span style="color: #ef4444;">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                    @error('name') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="category_id">Kategori <span style="color: #ef4444;">*</span></label>
                    <select id="category_id" name="category_id" class="form-control" required>
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @if(old('category_id', $product->category_id) == $category->id) selected @endif>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="unit_id">Satuan <span style="color: #ef4444;">*</span></label>
                    <select id="unit_id" name="unit_id" class="form-control" required>
                        <option value="">Pilih Satuan</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}" @if(old('unit_id', $product->unit_id) == $unit->id) selected @endif>{{ $unit->name }}</option>
                        @endforeach
                    </select>
                    @error('unit_id') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="purchase_price_display">Harga Beli <span style="color: #ef4444;">*</span></label>
                    <input type="text" id="purchase_price_display" class="form-control" value="{{ old('purchase_price', $product->purchase_price) ? number_format(old('purchase_price', $product->purchase_price), 0, ',', '.') : '' }}" required oninput="formatCurrency(this, 'purchase_price')">
                    <input type="hidden" id="purchase_price" name="purchase_price" value="{{ old('purchase_price', $product->purchase_price) }}">
                    @error('purchase_price') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="selling_price_display">Harga Jual <span style="color: #ef4444;">*</span></label>
                    <input type="text" id="selling_price_display" class="form-control" value="{{ old('selling_price', $product->selling_price) ? number_format(old('selling_price', $product->selling_price), 0, ',', '.') : '' }}" required oninput="formatCurrency(this, 'selling_price')">
                    <input type="hidden" id="selling_price" name="selling_price" value="{{ old('selling_price', $product->selling_price) }}">
                    @error('selling_price') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="minimum_stock">Minimal Stok</label>
                    <input type="number" id="minimum_stock" name="minimum_stock" class="form-control" value="{{ old('minimum_stock', $product->minimum_stock) }}" min="0">
                    @error('minimum_stock') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-group" style="margin-top: 1rem;">
                <label for="description">Deskripsi</label>
                <textarea id="description" name="description" class="form-control" rows="3">{{ old('description', $product->description) }}</textarea>
                @error('description') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('kepala-cabang.products.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>

    <script>
        function formatCurrency(input, hiddenId) {
            let value = input.value.replace(/[^0-9]/g, '');
            document.getElementById(hiddenId).value = value;
            if (value) {
                input.value = new Intl.NumberFormat('id-ID').format(value);
            } else {
                input.value = '';
            }
        }
    </script>
@endsection

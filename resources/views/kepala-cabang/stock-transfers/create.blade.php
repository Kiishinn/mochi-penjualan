@extends('layouts.dashboard')

@section('title', 'Buat Perpindahan Barang')
@section('page-title', 'Buat Perpindahan Barang')

@section('content')
    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <div class="card-header">
            <h3>Form Perpindahan Barang ke Cabang Lain</h3>
        </div>

        <form action="{{ route('kepala-cabang.stock-transfers.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="date">Tanggal</label>
                <input type="date" id="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
                @error('date') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="to_branch_id">Cabang Tujuan <span style="color: #ef4444;">*</span></label>
                <select id="to_branch_id" name="to_branch_id" class="form-control" required>
                    <option value="">Pilih Cabang Tujuan</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" @if(old('to_branch_id') == $branch->id) selected @endif>{{ $branch->name }}</option>
                    @endforeach
                </select>
                @error('to_branch_id') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="product_id">Produk <span style="color: #ef4444;">*</span></label>
                <select id="product_id" name="product_id" class="form-control" required onchange="updateMaxStock()">
                    <option value="">Pilih Produk</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-stock="{{ $stocks[$product->id] ?? 0 }}" @if(old('product_id') == $product->id) selected @endif>
                            {{ $product->name }} (Stok saat ini: {{ $stocks[$product->id] ?? 0 }})
                        </option>
                    @endforeach
                </select>
                @error('product_id') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="quantity">Jumlah Pindah <span style="color: #ef4444;">*</span></label>
                <input type="number" id="quantity" name="quantity" class="form-control" value="{{ old('quantity') }}" required min="1">
                @error('quantity') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="note">Keterangan</label>
                <textarea id="note" name="note" class="form-control" rows="3">{{ old('note') }}</textarea>
                @error('note') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Kirim Permintaan</button>
                <a href="{{ route('kepala-cabang.stock-transfers.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>

    <script>
        function updateMaxStock() {
            const select = document.getElementById('product_id');
            const qtyInput = document.getElementById('quantity');
            const selectedOption = select.options[select.selectedIndex];
            
            if (selectedOption.value) {
                const maxStock = selectedOption.getAttribute('data-stock');
                qtyInput.max = maxStock;
                qtyInput.placeholder = `Maksimal: ${maxStock}`;
            } else {
                qtyInput.removeAttribute('max');
                qtyInput.placeholder = '';
            }
        }
        
        // Inisialisasi saat load
        document.addEventListener('DOMContentLoaded', updateMaxStock);
    </script>
@endsection

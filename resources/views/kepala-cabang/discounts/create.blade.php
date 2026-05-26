@extends('layouts.dashboard')

@section('title', 'Ajukan Diskon Baru')
@section('page-title', 'Ajukan Diskon Baru')

@section('content')
    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <div class="card-header">
            <h3>Form Pengajuan Diskon</h3>
        </div>
        <div style="padding: 1.5rem;">
            <form action="{{ route('kepala-cabang.discounts.store') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label>Pilih Produk</label>
                    <select name="product_id" class="form-control tom-select" required>
                        <option value="">-- Pilih Produk --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }} (Harga: Rp {{ number_format($product->selling_price, 0, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Tipe Diskon</label>
                    <select name="discount_type" class="form-control" required>
                        <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                        <option value="nominal" {{ old('discount_type') == 'nominal' ? 'selected' : '' }}>Nominal Potongan Harga (Rp)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Nilai Diskon</label>
                    <input type="number" name="discount_value" class="form-control" value="{{ old('discount_value') }}" required min="1" step="0.01" placeholder="Misal: 10 untuk 10%, atau 5000 untuk potongan 5rb">
                    <small style="color: var(--text-secondary); margin-top: 0.25rem; display: block;">Masukkan angka saja tanpa simbol % atau Rp.</small>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>Waktu Mulai Berlaku</label>
                        <input type="datetime-local" name="start_date" class="form-control" value="{{ old('start_date') }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Waktu Berakhir</label>
                        <input type="datetime-local" name="end_date" class="form-control" value="{{ old('end_date') }}" required>
                    </div>
                </div>
                
                <div style="display: flex; gap: 1rem; margin-top: 1.5rem; justify-content: flex-end;">
                    <a href="{{ route('kepala-cabang.discounts.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new TomSelect('.tom-select',{
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                }
            });
        });
    </script>
@endsection

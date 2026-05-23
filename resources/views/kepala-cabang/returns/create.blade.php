@extends('layouts.dashboard')

@section('title', 'Buat Pengajuan Retur')
@section('page-title', 'Buat Pengajuan Retur')

@section('content')
    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <div class="card-header">
            <h3>Form Retur Barang (Dari Pelanggan)</h3>
        </div>

        <form action="{{ route('kepala-cabang.returns.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="sale_id">Pilih Transaksi (No Invoice) <span style="color: #ef4444;">*</span></label>
                <select id="sale_id" name="sale_id" class="form-control" required onchange="loadProducts(this)">
                    <option value="">Pilih Transaksi</option>
                    @foreach($sales as $sale)
                        <option value="{{ $sale->id }}" data-details="{{ json_encode($sale->details->map(function($d) { return ['id' => $d->product_id, 'name' => $d->product->name, 'qty' => $d->quantity]; })) }}">
                            {{ $sale->invoice_number }} - {{ date('d/m/Y', strtotime($sale->transaction_date)) }}
                        </option>
                    @endforeach
                </select>
                @error('sale_id') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="product_id">Pilih Produk <span style="color: #ef4444;">*</span></label>
                <select id="product_id" name="product_id" class="form-control" required>
                    <option value="">Pilih Produk (Pilih transaksi dulu)</option>
                </select>
                @error('product_id') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="quantity">Jumlah Retur <span style="color: #ef4444;">*</span></label>
                <input type="number" id="quantity" name="quantity" class="form-control" value="{{ old('quantity') }}" required min="1">
                @error('quantity') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="reason">Alasan Retur</label>
                <textarea id="reason" name="reason" class="form-control" rows="3">{{ old('reason') }}</textarea>
                @error('reason') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Ajukan Retur</button>
                <a href="{{ route('kepala-cabang.returns.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>

    <script>
        function loadProducts(select) {
            const productSelect = document.getElementById('product_id');
            productSelect.innerHTML = '<option value="">Pilih Produk</option>';
            
            if (select.selectedIndex > 0) {
                const option = select.options[select.selectedIndex];
                const details = JSON.parse(option.getAttribute('data-details'));
                
                details.forEach(detail => {
                    const opt = document.createElement('option');
                    opt.value = detail.id;
                    opt.textContent = `${detail.name} (Beli: ${detail.qty})`;
                    productSelect.appendChild(opt);
                });
            }
        }
    </script>
@endsection

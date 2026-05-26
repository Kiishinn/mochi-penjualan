@extends('layouts.dashboard')

@section('title', 'Buat Pengajuan Retur')
@section('page-title', 'Buat Pengajuan Retur')

@section('content')
    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <div class="card-header">
            <h3>Form Retur Barang (Dari Pelanggan)</h3>
        </div>

        <form action="{{ route('kasir.returns.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="sale_id">Pilih Transaksi (No Invoice) <span style="color: #ef4444;">*</span></label>
                <select id="sale_id" name="sale_id" class="form-control searchable-select" required onchange="loadProducts(this)">
                    <option value="">Pilih Transaksi</option>
                    @foreach($sales as $sale)
                        @php
                            $returnableDetails = $sale->details->map(function($d) use ($sale) {
                                $alreadyReturned = $sale->returnItems->where('product_id', $d->product_id)
                                    ->filter(function($r) {
                                        return $r->status !== 'rejected';
                                    })->sum('quantity');
                                $remaining = $d->quantity - $alreadyReturned;
                                
                                if ($remaining > 0) {
                                    return ['id' => $d->product_id, 'name' => $d->product->name, 'qty' => $remaining];
                                }
                                return null;
                            })->filter()->values();
                        @endphp
                        @if($returnableDetails->count() > 0)
                            <option value="{{ $sale->id }}" data-details="{{ json_encode($returnableDetails) }}" @if(old('sale_id') == $sale->id) selected @endif>
                                {{ $sale->invoice_number }} - {{ date('d/m/Y', strtotime($sale->transaction_date)) }}
                            </option>
                        @endif
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
                <label for="return_type">Tipe Penyelesaian (Retur) <span style="color: #ef4444;">*</span></label>
                <select id="return_type" name="return_type" class="form-control" required>
                    <option value="refund" @if(old('return_type') == 'refund') selected @endif>Kembali Uang (Refund)</option>
                    <option value="exchange" @if(old('return_type') == 'exchange') selected @endif>Tukar Barang Sama (Exchange)</option>
                </select>
                @error('return_type') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="item_condition">Kondisi Barang Diretur <span style="color: #ef4444;">*</span></label>
                <select id="item_condition" name="item_condition" class="form-control" required>
                    <option value="good" @if(old('item_condition') == 'good') selected @endif>Masih Bagus (Bisa Dijual Lagi)</option>
                    <option value="damaged" @if(old('item_condition') == 'damaged') selected @endif>Rusak/Cacat (Buang)</option>
                </select>
                @error('item_condition') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="reason">Alasan Retur</label>
                <textarea id="reason" name="reason" class="form-control" rows="3" placeholder="Wajib dibawa: Barang fisik & Nota Asli">{{ old('reason') }}</textarea>
                @error('reason') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Ajukan Retur (Butuh Approval KC)</button>
                <a href="{{ route('kasir.returns.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>

    <script>
        function loadProducts(select) {
            const productSelect = document.getElementById('product_id');
            productSelect.innerHTML = '<option value="">Pilih Produk</option>';
            
            if (select.value) {
                const option = Array.from(select.options).find(opt => opt.value === select.value);
                if (option) {
                    const details = JSON.parse(option.getAttribute('data-details'));
                    
                    details.forEach(detail => {
                        const opt = document.createElement('option');
                        opt.value = detail.id;
                        opt.textContent = `${detail.name} (Beli: ${detail.qty})`;
                        productSelect.appendChild(opt);
                    });
                    
                    if (details.length === 1) {
                        productSelect.value = details[0].id;
                    }
                }
            }
        }
    </script>
@endsection

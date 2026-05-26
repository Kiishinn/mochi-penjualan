@extends('layouts.dashboard')

@section('title', 'Buat Permintaan Barang')
@section('page-title', 'Buat Permintaan Stok Baru')

@section('content')
    <div class="card" style="max-width: 900px; margin: 0 auto;">
        <div class="card-header">
            <h3>Pilih Cabang Sumber</h3>
        </div>

        <!-- Form untuk memilih cabang sumber (mengubah URL) -->
        <form method="GET" action="{{ route('kepala-cabang.stock-transfers.create') }}" style="padding: 1.5rem; border-bottom: 1px solid var(--border-color);">
            <div class="form-group" style="margin-bottom: 0;">
                <label for="source_branch_id">Pilih Cabang yang Ingin Dimintai Stok <span style="color: #ef4444;">*</span></label>
                <select id="source_branch_id" name="source_branch_id" class="form-control searchable-select" required onchange="this.form.submit()">
                    <option value="">-- Pilih Cabang --</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" @if($selectedBranch == $branch->id) selected @endif>{{ $branch->name }}</option>
                    @endforeach
                </select>
                <div class="form-error" style="color: var(--text-muted); margin-top: 0.5rem;">
                    Pilih cabang untuk melihat ketersediaan stok mereka.
                </div>
            </div>
        </form>

        @if($selectedBranch)
        <!-- Form utama untuk mengirim permintaan -->
        <form action="{{ route('kepala-cabang.stock-transfers.store') }}" method="POST" id="request-form" style="padding: 1.5rem;">
            @csrf
            <input type="hidden" name="from_branch_id" value="{{ $selectedBranch }}">
            
            <div class="form-grid" style="margin-bottom: 2rem;">
                <div class="form-group">
                    <label for="date">Tanggal Pengajuan <span style="color: #ef4444;">*</span></label>
                    <input type="datetime-local" id="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d\TH:i')) }}" required>
                    @error('date') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label for="requester_note">Pesan / Alasan Meminta Stok (Opsional)</label>
                    <textarea id="requester_note" name="requester_note" class="form-control" rows="2" placeholder="Contoh: Stok kami habis karena ada event, mohon segera dikirim">{{ old('requester_note') }}</textarea>
                    @error('requester_note') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <hr style="border: 0; border-top: 1px solid var(--border-color); margin-bottom: 1.5rem;">

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h4 style="font-weight: 600;">Daftar Barang yang Diminta</h4>
                <button type="button" class="btn btn-secondary btn-sm" onclick="addRow()">+ Tambah Baris</button>
            </div>

            @error('items') <div class="alert alert-error" style="margin-bottom: 1rem; color: var(--danger);">{{ $message }}</div> @enderror
            @error('error') <div class="alert alert-error" style="margin-bottom: 1rem; color: var(--danger);">{{ $message }}</div> @enderror

            <div style="overflow-x: auto;">
                <table class="data-table" id="items-table">
                    <thead>
                        <tr>
                            <th>Produk (Stok Tersedia di Cabang Tujuan)</th>
                            <th style="width: 150px;">Jumlah Diminta</th>
                            <th style="width: 80px; text-align: center;">Hapus</th>
                        </tr>
                    </thead>
                    <tbody id="table-body">
                        <!-- Baris Pertama (Wajib) -->
                        <tr class="item-row">
                            <td>
                                <select name="items[0][product_id]" class="form-control product-select searchable-select" required onchange="updateMaxStock(this)">
                                    <option value="">-- Pilih Produk --</option>
                                    @foreach($allStocks as $stock)
                                        <option value="{{ $stock->product_id }}" data-stock="{{ $stock->quantity }}">
                                            {{ $stock->product->name }} (Tersedia: {{ $stock->quantity }})
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" name="items[0][quantity_requested]" class="form-control qty-input" required min="1" placeholder="Qty">
                            </td>
                            <td style="text-align: center;">
                                <button type="button" class="btn btn-sm" style="color: var(--danger); background: transparent; border: none; font-size: 1.25rem;" onclick="removeRow(this)" disabled>&times;</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="form-actions" style="margin-top: 2rem;">
                <button type="submit" class="btn btn-primary" id="btn-submit">Kirim Permintaan Stok</button>
                <a href="{{ route('kepala-cabang.stock-transfers.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
        @endif
    </div>

    @if($selectedBranch)
    <!-- Template for new rows -->
    <template id="row-template">
        <tr class="item-row">
            <td>
                <select name="items[__INDEX__][product_id]" class="form-control product-select searchable-select" required onchange="updateMaxStock(this)">
                    <option value="">-- Pilih Produk --</option>
                    @foreach($allStocks as $stock)
                        <option value="{{ $stock->product_id }}" data-stock="{{ $stock->quantity }}">
                            {{ $stock->product->name }} (Tersedia: {{ $stock->quantity }})
                        </option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" name="items[__INDEX__][quantity_requested]" class="form-control qty-input" required min="1" placeholder="Qty">
            </td>
            <td style="text-align: center;">
                <button type="button" class="btn btn-sm btn-remove-row" style="color: var(--danger); background: transparent; border: none; font-size: 1.25rem; cursor: pointer;" onclick="removeRow(this)">&times;</button>
            </td>
        </tr>
    </template>

    <script>
        let rowCount = 1;

        function updateMaxStock(selectElem) {
            if (!selectElem.value) return;
            
            const selects = document.querySelectorAll('select.product-select');
            let isDuplicate = false;
            
            selects.forEach(s => {
                if (s !== selectElem && s.value === selectElem.value) {
                    isDuplicate = true;
                }
            });
            
            if (isDuplicate) {
                alert("Produk ini sudah dipilih di baris lain. Silakan ubah jumlahnya pada baris tersebut.");
                if (selectElem.tomselect) {
                    selectElem.tomselect.clear();
                } else {
                    selectElem.value = "";
                }
                return;
            }

            const row = selectElem.closest('tr');
            const qtyInput = row.querySelector('.qty-input');
            const selectedOption = selectElem.options[selectElem.selectedIndex];
            
            if (selectedOption.value) {
                const maxStock = selectedOption.getAttribute('data-stock');
                qtyInput.max = maxStock;
                qtyInput.placeholder = `Maks: ${maxStock}`;
            } else {
                qtyInput.removeAttribute('max');
                qtyInput.placeholder = 'Qty';
            }
        }

        function addRow() {
            const template = document.getElementById('row-template').innerHTML;
            const newHtml = template.replace(/__INDEX__/g, rowCount);
            document.getElementById('table-body').insertAdjacentHTML('beforeend', newHtml);
            
            const newSelect = document.querySelector(`select[name="items[${rowCount}][product_id]"]`);
            if (newSelect && typeof TomSelect !== 'undefined') {
                new TomSelect(newSelect, { create: false });
            }
            
            rowCount++;
            updateRemoveButtons();
        }

        function removeRow(btn) {
            const row = btn.closest('tr');
            if (document.querySelectorAll('.item-row').length > 1) {
                row.remove();
                updateRemoveButtons();
            }
        }

        function updateRemoveButtons() {
            const rows = document.querySelectorAll('.item-row');
            const btns = document.querySelectorAll('.btn-remove-row');
            if (rows.length === 1) {
                if (btns[0]) btns[0].disabled = true;
            } else {
                btns.forEach(b => b.disabled = false);
            }
        }

        document.getElementById('request-form').addEventListener('submit', function(e) {
            const products = document.querySelectorAll('select.product-select');
            let hasError = false;
            let selectedIds = new Set();

            products.forEach(select => {
                if(selectedIds.has(select.value) && select.value !== "") {
                    hasError = true;
                }
                selectedIds.add(select.value);
            });

            if(hasError) {
                e.preventDefault();
                alert('Terdapat produk yang sama dipilih lebih dari satu kali. Silakan gabungkan jumlahnya dalam 1 baris.');
            }
        });
    </script>
    @endif
@endsection

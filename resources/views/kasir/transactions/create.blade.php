@extends('layouts.dashboard')

@section('title', 'Point of Sales')
@section('page-title', 'Point of Sales (Kasir)')

@section('content')
    <div class="pos-grid">
        
        <!-- Kolom Kiri: Produk -->
        <div class="card">
            <div class="card-header" style="flex-wrap: wrap; gap: 1rem;">
                <h3>Daftar Produk</h3>
                <input type="text" id="search-product" class="form-control" style="max-width: 250px;" placeholder="Cari nama produk...">
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; max-height: 600px; overflow-y: auto; padding-right: 0.5rem;" id="product-grid">
                @foreach($stocks as $stock)
                    @php
                        $discount = $stock->product->discounts->first();
                        $originalPrice = $stock->product->selling_price;
                        $finalPrice = $originalPrice;
                        $discountAmount = 0;
                        if ($discount) {
                            if ($discount->discount_type == 'percentage') {
                                $discountAmount = $finalPrice * ($discount->discount_value / 100);
                            } else {
                                $discountAmount = $discount->discount_value;
                            }
                            $finalPrice -= $discountAmount;
                            if ($finalPrice < 0) $finalPrice = 0;
                        }
                    @endphp
                    <div class="product-item card" style="padding: 1rem; cursor: pointer; transition: transform 0.1s; border: 1px solid var(--border-color);" 
                         onclick="addToCart({{ $stock->product->id }}, '{{ addslashes($stock->product->name) }}', {{ $finalPrice }}, {{ $stock->quantity }}, {{ $discountAmount }}, {{ $originalPrice }})"
                         data-name="{{ strtolower($stock->product->name) }}">
                        
                        <div style="font-weight: 600; color: var(--text-primary); line-height: 1.3;">{{ $stock->product->name }}</div>
                        <div style="font-size: 0.7rem; color: var(--accent); margin-bottom: 0.5rem; display: inline-block; padding: 2px 6px; background: rgba(6, 182, 212, 0.1); border-radius: 4px;">{{ $stock->product->category->name ?? 'Umum' }}</div>
                        <div style="font-size: 0.8125rem; font-weight: 700; margin-bottom: 0.5rem;">
                            @if($discount)
                                <span style="text-decoration: line-through; color: var(--text-muted); font-size: 0.7rem; margin-right: 0.25rem;">Rp {{ number_format($originalPrice, 0, ',', '.') }}</span>
                                <span style="color: var(--danger);">Rp {{ number_format($finalPrice, 0, ',', '.') }}</span>
                            @else
                                <span style="color: var(--accent);">Rp {{ number_format($finalPrice, 0, ',', '.') }}</span>
                            @endif
                        </div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary);">Stok: {{ $stock->quantity }} {{ $stock->product->unit->name ?? '' }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Kolom Kanan: Keranjang & Pembayaran -->
        <div class="card" style="display: flex; flex-direction: column; height: 100%;">
            <div class="card-header">
                <h3>Keranjang</h3>
                <button type="button" class="btn btn-sm" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;" onclick="clearCart()">Kosongkan</button>
            </div>

            <!-- List Item di Keranjang -->
            <div id="cart-items" style="flex: 1; overflow-y: auto; margin-bottom: 1rem;">
                <div style="text-align: center; color: var(--text-muted); margin-top: 2rem;">Keranjang masih kosong</div>
            </div>

            <hr style="border: 0; border-top: 1px dashed var(--border-color); margin-bottom: 1rem;">

            <!-- Rincian Total -->
            <div style="margin-bottom: 1rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 1.125rem; font-weight: 700; color: var(--text-primary);">
                    <span>Total Tagihan</span>
                    <span id="grand-total">Rp 0</span>
                </div>
            </div>

            <!-- Form Pembayaran -->
            <form action="{{ route('kasir.transactions.store') }}" method="POST" id="checkout-form">
                @csrf
                <input type="hidden" name="cart" id="cart-input">
                
                <div class="form-group">
                    <label for="paid_amount_text">Uang Bayar (Tunai)</label>
                    <input type="text" id="paid_amount_text" class="form-control" style="font-size: 1.25rem; font-weight: 700; padding: 1rem;" required oninput="formatAndCalculateChange(this)" placeholder="0">
                    <input type="hidden" id="paid_amount" name="paid_amount">
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label>Kembalian</label>
                    <div id="change_amount_display" style="font-size: 1.25rem; font-weight: 700; color: #22c55e; padding: 0.5rem 0;">Rp 0</div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 1rem; font-size: 1rem;" id="btn-checkout" disabled>Proses Pembayaran</button>
            </form>
        </div>
    </div>

    <style>
        .product-item:hover { transform: translateY(-3px); border-color: var(--accent); background: rgba(6, 182, 212, 0.05); }
        .cart-item { display: flex; justify-content: space-between; align-items: flex-start; padding: 0.75rem 0; border-bottom: 1px solid var(--border-color); }
        .cart-qty-btn { background: var(--bg-input); border: 1px solid var(--border-color); color: var(--text-primary); width: 24px; height: 24px; border-radius: 4px; cursor: pointer; display: flex; align-items: center; justify-content: center; }
        .cart-qty-btn:hover { background: var(--border-color); }
    </style>

    <script>
        let cart = [];
        let grandTotal = 0;

        // Pencarian Produk
        document.getElementById('search-product').addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase();
            const items = document.querySelectorAll('.product-item');
            items.forEach(item => {
                const name = item.getAttribute('data-name');
                if(name.includes(term)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
        }

        function addToCart(id, name, price, maxStock, discountAmount = 0, originalPrice = 0) {
            const existing = cart.find(item => item.product_id === id);
            if (existing) {
                if (existing.qty < maxStock) {
                    existing.qty++;
                } else {
                    alert('Stok tidak mencukupi!');
                }
            } else {
                if (maxStock > 0) {
                    cart.push({ product_id: id, name: name, price: price, qty: 1, maxStock: maxStock, discount_amount: discountAmount, original_price: originalPrice });
                } else {
                    alert('Stok kosong!');
                }
            }
            renderCart();
        }

        function updateQty(id, change) {
            const item = cart.find(i => i.product_id === id);
            if(item) {
                const newQty = item.qty + change;
                if(newQty > 0 && newQty <= item.maxStock) {
                    item.qty = newQty;
                } else if(newQty === 0) {
                    cart = cart.filter(i => i.product_id !== id);
                } else if(newQty > item.maxStock) {
                    alert('Stok tidak mencukupi!');
                }
            }
            renderCart();
        }

        function setQty(id, value) {
            const item = cart.find(i => i.product_id === id);
            if(item) {
                let newQty = parseInt(value, 10);
                if (isNaN(newQty) || newQty <= 0) {
                    // Jika dihapus atau diinput 0, minimal 1
                    newQty = 1;
                }
                
                if (newQty > item.maxStock) {
                    alert('Stok tidak mencukupi! Sisa stok: ' + item.maxStock);
                    newQty = item.maxStock;
                }
                
                item.qty = newQty;
            }
            renderCart();
        }

        function clearCart() {
            cart = [];
            document.getElementById('paid_amount_text').value = '';
            document.getElementById('paid_amount').value = '';
            renderCart();
        }

        function renderCart() {
            const container = document.getElementById('cart-items');
            if (cart.length === 0) {
                container.innerHTML = '<div style="text-align: center; color: var(--text-muted); margin-top: 2rem;">Keranjang masih kosong</div>';
                grandTotal = 0;
            } else {
                let html = '';
                grandTotal = 0;
                cart.forEach(item => {
                    const subtotal = item.price * item.qty;
                    grandTotal += subtotal;
                    html += `
                        <div class="cart-item">
                            <div style="flex: 1;">
                                <div style="font-weight: 600; font-size: 0.875rem; margin-bottom: 0.25rem;">${item.name}</div>
                                <div style="font-size: 0.75rem; color: var(--text-secondary);">
                                    ${item.discount_amount > 0 ? `<span style="text-decoration: line-through; color: var(--text-muted); margin-right: 4px;">${formatRupiah(item.original_price)}</span>` : ''}
                                    ${formatRupiah(item.price)}
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <button type="button" class="cart-qty-btn" onclick="updateQty(${item.product_id}, -1)">-</button>
                                <input type="text" 
                                       inputmode="numeric"
                                       style="width: 50px; text-align: center; padding: 0.25rem; font-size: 0.875rem; background: var(--bg-input); color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 4px; outline: none;" 
                                       value="${item.qty}" 
                                       onchange="setQty(${item.product_id}, this.value)"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                <button type="button" class="cart-qty-btn" onclick="updateQty(${item.product_id}, 1)">+</button>
                            </div>
                        </div>
                    `;
                });
                container.innerHTML = html;
            }

            document.getElementById('grand-total').innerText = formatRupiah(grandTotal);
            document.getElementById('cart-input').value = JSON.stringify(cart);
            
            calculateChange();
            
            document.getElementById('btn-checkout').disabled = cart.length === 0;
        }

        function formatAndCalculateChange(input) {
            let val = input.value.replace(/[^0-9]/g, '');
            if(val === '') {
                input.value = '';
                document.getElementById('paid_amount').value = '';
                calculateChange();
                return;
            }
            
            input.value = parseInt(val, 10).toLocaleString('id-ID');
            document.getElementById('paid_amount').value = val;
            calculateChange();
        }

        function calculateChange() {
            const paidInput = document.getElementById('paid_amount').value;
            const paid = parseFloat(paidInput) || 0;
            const btnCheckout = document.getElementById('btn-checkout');
            const display = document.getElementById('change_amount_display');
            
            if (cart.length > 0 && paid >= grandTotal) {
                const change = paid - grandTotal;
                display.innerText = formatRupiah(change);
                display.style.color = '#22c55e';
                btnCheckout.disabled = false;
            } else {
                display.innerText = "Rp 0 (Uang Kurang)";
                display.style.color = '#ef4444';
                btnCheckout.disabled = true;
            }
        }

        document.getElementById('checkout-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const paidInput = document.getElementById('paid_amount').value;
            const paid = parseFloat(paidInput) || 0;
            const change = paid - grandTotal;

            window.swalDark.fire({
                title: 'Konfirmasi Pembayaran',
                html: `
                    <div style="text-align: left; margin-top: 1rem;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span>Total Belanja:</span>
                            <strong>${formatRupiah(grandTotal)}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span>Uang Tunai:</span>
                            <strong>${formatRupiah(paid)}</strong>
                        </div>
                        <hr style="border: 0; border-top: 1px dashed var(--border-color); margin: 0.5rem 0;">
                        <div style="display: flex; justify-content: space-between; font-size: 1.125rem; font-weight: bold; color: #22c55e;">
                            <span>Kembalian:</span>
                            <span>${formatRupiah(change)}</span>
                        </div>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Proses Transaksi',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });

    </script>
@endsection

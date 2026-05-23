@extends('layouts.dashboard')

@section('title', 'Buka Shift Kasir')
@section('page-title', 'Buka Shift Baru')

@section('content')
    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="width: 80px; height: 80px; background: rgba(6, 182, 212, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#06b6d4" style="width: 40px; height: 40px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            <h2 style="font-size: 1.5rem; color: var(--text-primary); margin-bottom: 0.5rem;">Mulai Shift Baru</h2>
            <p style="color: var(--text-muted);">Masukkan saldo awal laci kasir (modal kembalian) untuk memulai shift Anda hari ini.</p>
        </div>

        <form method="POST" action="{{ route('kasir.shifts.store') }}">
            @csrf
            
            <div class="form-group">
                <label for="starting_cash_display">Saldo Awal Laci (Rp) <span style="color: var(--danger);">*</span></label>
                <input type="text" inputmode="numeric" id="starting_cash_display" class="form-control" style="font-size: 1.25rem; font-weight: 600; padding: 1rem;" placeholder="0" value="{{ old('starting_cash', 0) }}" required autofocus onkeyup="formatCurrency(this, 'starting_cash')">
                <input type="hidden" id="starting_cash" name="starting_cash" value="{{ old('starting_cash', 0) }}">
                @error('starting_cash')
                    <p class="form-error">{{ $message }}</p>
                @enderror
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.5rem;">Uang fisik yang ada di laci sebelum ada transaksi penjualan.</p>
            </div>

            <div class="form-actions" style="margin-top: 2rem;">
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.125rem;">
                    Buka Shift & Mulai Transaksi
                </button>
            </div>
            
            <div style="text-align: center; margin-top: 1rem;">
                <a href="{{ route('kasir.dashboard') }}" style="color: var(--text-muted); text-decoration: none; font-size: 0.875rem;">Kembali ke Dashboard</a>
            </div>
        </form>
    </div>

    <script>
        function formatCurrency(input, hiddenId) {
            // Remove all non-numeric characters
            let value = input.value.replace(/[^0-9]/g, '');
            
            // Set hidden value for form submission
            document.getElementById(hiddenId).value = value;
            
            // Format for display
            if (value) {
                input.value = new Intl.NumberFormat('id-ID').format(value);
            } else {
                input.value = '';
            }
        }
        
        // Initialize format on load
        document.addEventListener('DOMContentLoaded', function() {
            let displayInput = document.getElementById('starting_cash_display');
            if (displayInput.value) {
                formatCurrency(displayInput, 'starting_cash');
            }
        });
    </script>
@endsection

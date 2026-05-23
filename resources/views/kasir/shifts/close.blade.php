@extends('layouts.dashboard')

@section('title', 'Tutup Shift Kasir')
@section('page-title', 'Tutup Shift')

@section('content')
    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="width: 80px; height: 80px; background: rgba(239, 68, 68, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ef4444" style="width: 40px; height: 40px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                </svg>
            </div>
            <h2 style="font-size: 1.5rem; color: var(--text-primary); margin-bottom: 0.5rem;">Akhiri Shift Kerja</h2>
            <p style="color: var(--text-muted);">Hitung dan masukkan uang fisik yang ada di laci Anda saat ini.</p>
        </div>

        <div style="background: rgba(148, 163, 184, 0.05); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem;">
            <h4 style="margin-bottom: 1rem; color: var(--text-primary); font-size: 1rem;">Ringkasan Sistem:</h4>
            
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                <span style="color: var(--text-muted);">Waktu Buka Shift</span>
                <span style="font-weight: 500; color: var(--text-primary);">{{ $shift->start_time->format('d M Y, H:i') }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                <span style="color: var(--text-muted);">Modal Awal (Saldo Laci)</span>
                <span style="font-weight: 500; color: var(--text-primary);">Rp {{ number_format($shift->starting_cash, 0, ',', '.') }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                <span style="color: var(--text-muted);">Total Pemasukan Tunai</span>
                <span style="font-weight: 500; color: var(--text-primary); color: #22c55e;">+ Rp {{ number_format($totalSalesCash, 0, ',', '.') }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                <span style="color: var(--text-muted);">Total Transaksi (Struk)</span>
                <span style="font-weight: 500; color: var(--text-primary);">{{ $sales->count() }} Transaksi</span>
            </div>
            
            <hr style="border-color: var(--border-color); margin: 1rem 0;">
            
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="font-weight: 600; color: var(--text-primary);">Estimasi Uang di Laci</span>
                <span style="font-weight: 700; color: var(--text-primary); font-size: 1.25rem;">Rp {{ number_format($expectedCash, 0, ',', '.') }}</span>
            </div>
        </div>

        <form method="POST" action="{{ route('kasir.shifts.update') }}">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="ending_cash_actual_display">Uang Aktual di Laci (Rp) <span style="color: var(--danger);">*</span></label>
                <input type="text" inputmode="numeric" id="ending_cash_actual_display" class="form-control" style="font-size: 1.25rem; font-weight: 600; padding: 1rem;" placeholder="0" value="{{ old('ending_cash_actual') }}" required autofocus onkeyup="formatCurrency(this, 'ending_cash_actual')">
                <input type="hidden" id="ending_cash_actual" name="ending_cash_actual" value="{{ old('ending_cash_actual') }}">
                @error('ending_cash_actual')
                    <p class="form-error">{{ $message }}</p>
                @enderror
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.5rem;">Hitung dengan teliti uang fisik di laci Anda sebelum menutup shift.</p>
            </div>

            <div class="form-actions" style="margin-top: 2rem;">
                <button type="submit" class="btn" style="width: 100%; padding: 1rem; font-size: 1.125rem; background: var(--danger); color: white; border: none;" onclick="return confirm('Apakah Anda yakin uang fisik yang diinput sudah benar? Shift akan ditutup dan tidak dapat dibuka kembali.')">
                    Tutup Shift
                </button>
            </div>
            
            <div style="text-align: center; margin-top: 1rem;">
                <a href="{{ route('kasir.dashboard') }}" style="color: var(--text-muted); text-decoration: none; font-size: 0.875rem;">Batal</a>
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
            let displayInput = document.getElementById('ending_cash_actual_display');
            if (displayInput.value) {
                formatCurrency(displayInput, 'ending_cash_actual');
            }
        });
    </script>
@endsection

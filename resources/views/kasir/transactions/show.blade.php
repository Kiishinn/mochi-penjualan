@extends('layouts.dashboard')

@section('title', 'Detail Transaksi')
@section('page-title', 'Detail Transaksi & Nota')

@section('content')
    <div style="display: flex; justify-content: center; margin-bottom: 1.5rem;">
        <button class="btn btn-primary" onclick="printReceipt()">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.728 9.348h10.544M6.728 12.348h10.544M6.728 15.348h10.544M3 6.75V19.5a2.25 2.25 0 0 0 2.25 2.25h13.5A2.25 2.25 0 0 0 21 19.5V6.75M3 6.75a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 6.75M3 6.75V4.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 4.5v2.25m-18 0h18" /></svg>
            Cetak Nota
        </button>
        <button class="btn btn-secondary" onclick="downloadPDF()" style="margin-left: 0.5rem; background-color: #f59e0b; color: white; border-color: #f59e0b;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
            Simpan PDF
        </button>
        <a href="{{ $backUrl ?? url()->previous() }}" class="btn btn-secondary" style="margin-left: 0.5rem;">Kembali</a>
        
        @if($transaction->created_at->diffInMinutes(now()) <= 15 && $transaction->returnItems->count() == 0)
            <form action="{{ route('kasir.transactions.void', $transaction->id) }}" method="POST" class="form-delete" style="margin-left: auto;">
                @csrf
                <button type="submit" class="btn" style="background: var(--danger); color: white; display: flex; align-items: center; gap: 0.5rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 1.25rem; height: 1.25rem;"><path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    Batalkan Transaksi (Void)
                </button>
            </form>
        @endif
    </div>

    <!-- Area Struk untuk diprint -->
    <div id="receipt-area" style="max-width: 400px; margin: 0 auto; background: #fff; color: #000; padding: 2rem 1.5rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); font-family: 'Courier New', Courier, monospace;">
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <h2 style="margin: 0; font-size: 1.25rem; font-weight: bold;">{{ $transaction->branch->name }}</h2>
            <p style="margin: 0.25rem 0 0; font-size: 0.875rem;">{{ $transaction->branch->address }}</p>
            <p style="margin: 0.25rem 0 0; font-size: 0.875rem;">Telp: {{ $transaction->branch->phone }}</p>
        </div>

        <div style="font-size: 0.875rem; margin-bottom: 1rem; border-bottom: 1px dashed #000; padding-bottom: 0.5rem;">
            <div style="display: flex; justify-content: space-between;">
                <span>No: {{ $transaction->invoice_number }}</span>
                <span>Tgl: {{ date('d/m/Y H:i', strtotime($transaction->transaction_date)) }}</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span>Kasir: {{ $transaction->user->name }}</span>
            </div>
        </div>

        <div style="margin-bottom: 1rem; border-bottom: 1px dashed #000; padding-bottom: 0.5rem;">
            <table style="width: 100%; font-size: 0.875rem; border-collapse: collapse;">
                @foreach($transaction->details as $detail)
                    @php
                        $returnedQty = $transaction->returnItems->where('product_id', $detail->product_id)->where('status', 'approved')->sum('quantity');
                    @endphp
                    <tr>
                        <td colspan="3" style="padding-bottom: 0.25rem;">
                            {{ $detail->product->name }}
                            @if($returnedQty > 0)
                                <span style="color: var(--danger); font-size: 0.75rem; font-weight: bold; margin-left: 5px;">(Diretur: {{ $returnedQty }})</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-bottom: 0.5rem;">{{ $detail->quantity }} x</td>
                        <td style="padding-bottom: 0.5rem; text-align: right;">
                            @if(isset($detail->discount_amount) && $detail->discount_amount > 0)
                                <span style="text-decoration: line-through; color: #888; font-size: 0.75rem; display: block;">{{ number_format($detail->price + $detail->discount_amount, 0, ',', '.') }}</span>
                            @endif
                            {{ number_format($detail->price, 0, ',', '.') }}
                        </td>
                        <td style="padding-bottom: 0.5rem; text-align: right;">{{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </table>
        </div>

        <div style="font-size: 0.875rem; margin-bottom: 1.5rem;">
            <div style="display: flex; justify-content: space-between; font-weight: bold; margin-bottom: 0.25rem;">
                <span>TOTAL</span>
                <span>Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                <span>Tunai</span>
                <span>Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span>Kembali</span>
                <span>Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <div style="text-align: center; font-size: 0.875rem;">
            <p style="margin: 0;">Terima Kasih</p>
            <p style="margin: 0.25rem 0 0;">Barang yang sudah dibeli tidak dapat ditukar/dikembalikan tanpa nota.</p>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function printReceipt() {
            const printContent = document.getElementById('receipt-area').innerHTML;
            const originalContent = document.body.innerHTML;
            
            document.body.innerHTML = printContent;
            document.body.style.background = '#fff';
            window.print();
            
            document.body.innerHTML = originalContent;
            document.body.style.background = '';
            location.reload();
        }

        function downloadPDF() {
            const element = document.getElementById('receipt-area');
            const opt = {
                margin:       [0.5, 0.5, 0.5, 0.5],
                filename:     'Nota_Mochi_Petshop_{{ $transaction->invoice_number }}.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, backgroundColor: '#ffffff' },
                jsPDF:        { unit: 'in', format: 'a5', orientation: 'portrait' }
            };

            // Tambahkan text 'Sedang memproses...' di tombol jika perlu (opsional)
            html2pdf().set(opt).from(element).save();
        }
    </script>
@endsection

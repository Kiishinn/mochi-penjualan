@extends('layouts.dashboard')

@section('title', 'Tambah Supplier')
@section('page-title', 'Tambah Supplier')

@section('content')
    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <div class="card-header">
            <h3>Form Tambah Supplier</h3>
        </div>

        <form action="{{ route('kepala-cabang.suppliers.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="name">Nama Perusahaan / Supplier <span style="color: #ef4444;">*</span></label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required autofocus>
                @error('name') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            
            <div class="form-grid" style="margin-bottom: 1.5rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="contact_person">Nama PIC / Sales</label>
                    <input type="text" id="contact_person" name="contact_person" class="form-control" value="{{ old('contact_person') }}" placeholder="Cth: Budi">
                    @error('contact_person') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="phone">No HP / WhatsApp</label>
                    <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="Cth: 0812xxx">
                    @error('phone') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-grid" style="margin-bottom: 1.5rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="Cth: sales@company.com">
                    @error('email') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="bank_account">Info Rekening Bank</label>
                    <input type="text" id="bank_account" name="bank_account" class="form-control" value="{{ old('bank_account') }}" placeholder="Cth: BCA 123456 a/n Budi">
                    @error('bank_account') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>
            
            <div class="form-group">
                <label for="address">Alamat Lengkap</label>
                <textarea id="address" name="address" class="form-control" rows="3">{{ old('address') }}</textarea>
                @error('address') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            
            <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem; margin-top: 1rem;">
                <input type="checkbox" id="is_active" name="is_active" value="1" checked style="width: 1.2rem; height: 1.2rem;">
                <label for="is_active" style="margin-bottom: 0; font-weight: 500;">Supplier Aktif</label>
            </div>
            <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.25rem; margin-bottom: 1.5rem;">Hilangkan centang jika Anda tidak lagi membeli dari supplier ini.</p>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan Supplier</button>
                <a href="{{ route('kepala-cabang.suppliers.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection

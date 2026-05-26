@extends('layouts.dashboard')

@section('title', 'Tambah User')
@section('page-title', 'Tambah User Baru')

@section('content')
    <div class="card" style="max-width: 800px;">
        <div class="card-header" style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem;">
            <h3>Informasi User</h3>
            <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                </svg>
                Kembali
            </a>
        </div>

        <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-grid">
                <!-- Nama -->
                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required autofocus>
                    @error('name')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    @error('email')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Username -->
                <div class="form-group">
                    <label for="username">Username (Opsional)</label>
                    <input type="text" id="username" name="username" class="form-control" value="{{ old('username') }}" autocomplete="off">
                    @error('username')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div class="form-group">
                    <label for="phone">Nomor Telepon</label>
                    <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone') }}">
                    @error('phone')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address -->
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label for="address">Alamat Lengkap</label>
                    <textarea id="address" name="address" class="form-control" rows="3">{{ old('address') }}</textarea>
                    @error('address')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required autocomplete="new-password">
                    @error('password')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Konfirmasi Password -->
                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required autocomplete="new-password">
                </div>

                <!-- Role -->
                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role" class="form-control" required onchange="toggleBranch()">
                        <option value="">-- Pilih Role --</option>
                        <option value="owner" {{ old('role') == 'owner' ? 'selected' : '' }}>Owner</option>
                        <option value="kepala_cabang" {{ old('role') == 'kepala_cabang' ? 'selected' : '' }}>Kepala Cabang</option>
                        <option value="kasir" {{ old('role') == 'kasir' ? 'selected' : '' }}>Kasir</option>
                    </select>
                    @error('role')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Branch -->
                <div class="form-group" id="branch-group" style="display: {{ in_array(old('role'), ['kepala_cabang', 'kasir']) ? 'block' : 'none' }};">
                    <label for="branch_id">Cabang</label>
                    <select id="branch_id" name="branch_id" class="form-control">
                        <option value="">-- Pilih Cabang --</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('branch_id')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status Aktif -->
                <div class="form-group" style="grid-column: 1 / -1; display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} style="width: 1rem; height: 1rem; accent-color: var(--primary);">
                    <label for="is_active" style="margin-bottom: 0; cursor: pointer;">Akun Aktif (Bisa Login)</label>
                </div>
            </div>

            <div style="border-top: 1px solid var(--border-color); padding-top: 1.5rem; margin-top: 1.5rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label for="photo_profile">Foto Profil (Opsional)</label>
                    <input type="file" id="photo_profile" name="photo_profile" class="form-control" accept="image/*">
                    <small style="color: var(--text-muted); font-size: 0.75rem;">Maksimal 2MB (Format: jpg, jpeg, png)</small>
                    @error('photo_profile')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="photo_ktp">Foto KTP (Opsional / Arsip)</label>
                    <input type="file" id="photo_ktp" name="photo_ktp" class="form-control" accept="image/*">
                    <small style="color: var(--text-muted); font-size: 0.75rem;">Maksimal 2MB. Disarankan untuk identifikasi dan keamanan.</small>
                    @error('photo_ktp')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="form-actions" style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                    </svg>
                    Simpan User
                </button>
            </div>
        </form>
    </div>

    <script>
        function toggleBranch() {
            const role = document.getElementById('role').value;
            const branchGroup = document.getElementById('branch-group');
            const branchInput = document.getElementById('branch_id');

            if (role === 'kepala_cabang' || role === 'kasir') {
                branchGroup.style.display = 'block';
                branchInput.required = true;
            } else {
                branchGroup.style.display = 'none';
                branchInput.required = false;
                branchInput.value = '';
            }
        }
        
        // Run on load just in case of validation errors
        document.addEventListener('DOMContentLoaded', function() {
            toggleBranch();
        });
    </script>
@endsection

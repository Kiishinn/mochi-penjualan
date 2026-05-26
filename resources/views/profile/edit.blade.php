@extends('layouts.dashboard')

@section('title', 'Profil Saya')
@section('page-title', 'Pengaturan Profil')

@section('content')
    <div class="card" style="max-width: 800px; margin: 0 auto;">
        <div class="card-header" style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem;">
            <h3>Profil Pengguna</h3>
            <span class="badge badge-{{ $user->role }}" style="font-size: 0.85rem;">{{ str_replace('_', ' ', $user->role) }}</span>
        </div>

        @if(session('success'))
            <div style="background: rgba(34, 197, 94, 0.1); border-left: 4px solid var(--success); color: var(--success); padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem;">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div style="background: rgba(239, 68, 68, 0.1); border-left: 4px solid var(--danger); color: var(--danger); padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem;">
                <ul style="margin: 0; padding-left: 1.5rem;">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

    <style>
        .profile-grid {
            display: grid; 
            grid-template-columns: 1fr 2fr; 
            gap: 2rem;
        }
        @media (max-width: 768px) {
            .profile-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <div class="profile-grid">
            
            <!-- Kolom Foto Profil -->
            <div>
                <h4 style="margin-top: 0; margin-bottom: 1rem; color: var(--text-primary); font-size: 1.1rem;">Foto Profil</h4>
                
                <div style="text-align: center; margin-bottom: 1rem;">
                    @if($user->photo_profile)
                        <img src="{{ Storage::url($user->photo_profile) }}" alt="Foto Profil" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 2px solid var(--accent); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                    @else
                        <div style="width: 150px; height: 150px; border-radius: 50%; background: var(--border-color); display: flex; align-items: center; justify-content: center; margin: 0 auto; color: var(--text-muted); font-size: 3rem; font-weight: 600;">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                    @endif
                </div>

                <form method="POST" action="{{ route('profile.updatePhoto') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-group" style="text-align: center;">
                        <label for="photo_profile" class="btn btn-sm" style="background: transparent; border: 1px dashed var(--accent); color: var(--accent); cursor: pointer; display: inline-block; padding: 0.5rem 1rem;">
                            Pilih Foto Baru
                        </label>
                        <input type="file" id="photo_profile" name="photo_profile" accept="image/*" style="display: none;" onchange="document.getElementById('file-name').textContent = this.files[0].name">
                        <div id="file-name" style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;"></div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm" style="width: 100%;">Upload Foto</button>
                </form>
            </div>

            <!-- Kolom Detail & Keamanan -->
            <div>
                <h4 style="margin-top: 0; margin-bottom: 1rem; color: var(--text-primary); font-size: 1.1rem;">Informasi Dasar</h4>
                <div style="background: var(--bg-input); padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                        <tr>
                            <td style="padding: 0.5rem 0; color: var(--text-muted); width: 30%;">Nama Lengkap</td>
                            <td style="padding: 0.5rem 0; font-weight: 500; color: var(--text-primary);">{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 0.5rem 0; color: var(--text-muted);">Username</td>
                            <td style="padding: 0.5rem 0; font-weight: 500; color: var(--text-primary);">{{ $user->username ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 0.5rem 0; color: var(--text-muted);">Cabang</td>
                            <td style="padding: 0.5rem 0; font-weight: 500; color: var(--text-primary);">{{ $user->branch->name ?? 'Semua Cabang (Pusat)' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 0.5rem 0; color: var(--text-muted);">Email / Telp</td>
                            <td style="padding: 0.5rem 0; font-weight: 500; color: var(--text-primary);">{{ $user->email }} <br> {{ $user->phone }}</td>
                        </tr>
                    </table>
                    <div style="margin-top: 1rem; font-size: 0.75rem; color: var(--warning);">
                        * Hubungi Owner jika Anda perlu mengubah data identitas dasar Anda.
                    </div>
                </div>

                <h4 style="margin-top: 0; margin-bottom: 1rem; color: var(--text-primary); font-size: 1.1rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">Ubah Keamanan (Password & PIN)</h4>
                <form method="POST" action="{{ route('profile.updateSecurity') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group">
                        <label for="current_password">Password Saat Ini <span style="color: var(--danger);">*</span></label>
                        <input type="password" id="current_password" name="current_password" class="form-control" required placeholder="Masukkan password saat ini untuk validasi">
                        <small style="color: var(--text-muted); font-size: 0.75rem;">Wajib diisi untuk mengubah Password atau PIN.</small>
                    </div>

                    <div class="form-grid" style="gap: 1rem;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="password">Password Baru</label>
                            <input type="password" id="password" name="password" class="form-control" placeholder="Minimal 6 karakter">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="password_confirmation">Konfirmasi Password Baru</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Ketik ulang password baru">
                        </div>
                    </div>



                    <div class="form-actions" style="margin-top: 1.5rem; justify-content: flex-start;">
                        <button type="submit" class="btn btn-primary">Simpan Keamanan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

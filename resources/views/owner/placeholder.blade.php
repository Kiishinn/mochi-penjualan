@extends('layouts.dashboard')

@section('title', $title)
@section('page-title', $title)

@section('content')
    <div class="card" style="text-align: center; padding: 4rem 2rem;">
        <div style="display: inline-flex; align-items: center; justify-content: center; width: 80px; height: 80px; border-radius: 20px; background: rgba(6, 182, 212, 0.1); color: var(--accent); margin-bottom: 1.5rem;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 40px; height: 40px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem;">Fitur {{ $title }}</h2>
        <p style="color: var(--text-secondary); max-width: 500px; margin: 0 auto;">
            Halaman ini adalah placeholder. Sebagai Owner, Anda memiliki hak akses untuk <strong>melihat</strong> data pada halaman ini. Modul tabel dan data untuk fitur ini sedang dalam tahap pengembangan.
        </p>
        
        <div style="margin-top: 2rem;">
            <a href="{{ route('owner.dashboard') }}" class="btn btn-primary">
                Kembali ke Dashboard
            </a>
        </div>
    </div>
@endsection

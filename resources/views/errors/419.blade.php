<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sesi Kedaluwarsa - Mochi Petshop</title>
    <meta http-equiv="refresh" content="2;url={{ route('login') }}">
    <style>
        body { font-family: 'Inter', sans-serif; background: #0f172a; color: #f1f5f9; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; text-align: center; }
        .card { background: rgba(30, 41, 59, 0.7); padding: 2rem; border-radius: 16px; border: 1px solid rgba(148, 163, 184, 0.1); }
        h1 { color: #f87171; }
        a { color: #06b6d4; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="card">
        <h1>419 | Sesi Kedaluwarsa</h1>
        <p>Sesi Anda telah berakhir (mungkin karena *browser* dibiarkan terbuka terlalu lama atau server baru saja *restart*).</p>
        <p>Mengarahkan Anda kembali ke halaman login dalam 2 detik...</p>
        <p><a href="{{ route('login') }}">Klik di sini jika tidak otomatis pindah</a></p>
    </div>
</body>
</html>

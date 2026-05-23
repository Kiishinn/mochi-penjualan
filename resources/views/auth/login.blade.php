<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — Mochi Petshop</title>
    <meta name="description" content="Login ke Sistem Informasi Penjualan dan Stok Multi Cabang Mochi Petshop">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            position: relative;
            overflow: hidden;
        }

        /* Animated background orbs */
        body::before, body::after {
            content: '';
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.3;
            z-index: 0;
            animation: float 8s ease-in-out infinite;
        }
        body::before {
            width: 500px; height: 500px;
            background: linear-gradient(135deg, #06b6d4, #3b82f6);
            top: -150px; left: -100px;
        }
        body::after {
            width: 400px; height: 400px;
            background: linear-gradient(135deg, #f59e0b, #ef4444);
            bottom: -100px; right: -80px;
            animation-delay: 4s;
            animation-direction: reverse;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(30px, -30px) scale(1.05); }
            50% { transform: translate(-20px, 20px) scale(0.95); }
            75% { transform: translate(10px, 10px) scale(1.02); }
        }

        .login-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 440px;
            padding: 1rem;
        }

        /* Brand header */
        .brand {
            text-align: center;
            margin-bottom: 2rem;
        }
        .brand-icon {
            width: 72px; height: 72px;
            margin: 0 auto 1rem;
            background: linear-gradient(135deg, #06b6d4, #3b82f6);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 32px rgba(6, 182, 212, 0.3);
            animation: pulse-glow 3s ease-in-out infinite;
        }
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 8px 32px rgba(6, 182, 212, 0.3); }
            50% { box-shadow: 0 8px 48px rgba(6, 182, 212, 0.5); }
        }
        .brand-icon svg {
            width: 40px; height: 40px;
            color: #fff;
        }
        .brand h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #f1f5f9;
            letter-spacing: -0.02em;
        }
        .brand p {
            font-size: 0.875rem;
            color: #94a3b8;
            margin-top: 0.25rem;
        }

        /* Card */
        .login-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(148, 163, 184, 0.1);
            border-radius: 24px;
            padding: 2.5rem 2rem;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.4);
        }
        .login-card h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #e2e8f0;
            text-align: center;
            margin-bottom: 2rem;
        }

        /* Form groups */
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 500;
            color: #cbd5e1;
            margin-bottom: 0.5rem;
        }
        .input-wrapper {
            position: relative;
        }
        .input-wrapper svg {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px; height: 18px;
            color: #64748b;
            transition: color 0.2s;
            pointer-events: none;
        }
        .input-wrapper input {
            width: 100%;
            padding: 0.75rem 2.5rem 0.75rem 2.75rem;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(148, 163, 184, 0.15);
            border-radius: 12px;
            font-family: 'Inter', sans-serif;
            font-size: 0.9375rem;
            color: #f1f5f9;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .input-wrapper input::placeholder {
            color: #475569;
        }
        .input-wrapper input:focus {
            border-color: #06b6d4;
            box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.15);
        }
        .input-wrapper input:focus ~ svg,
        .input-wrapper input:focus + svg {
            color: #06b6d4;
        }

        /* Toggle password */
        .toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #64748b;
            cursor: pointer;
            padding: 2px;
            display: flex;
            transition: color 0.2s;
            z-index: 10;
        }
        .toggle-password:hover { color: #94a3b8; }
        .toggle-password svg { 
            width: 18px; height: 18px; 
            position: static; 
            transform: none; 
            pointer-events: auto;
        }

        /* Error messages */
        .error-text {
            font-size: 0.8125rem;
            color: #f87171;
            margin-top: 0.375rem;
        }

        /* Alert banner */
        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 12px;
            padding: 0.75rem 1rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .alert-error svg {
            width: 18px; height: 18px;
            color: #f87171;
            flex-shrink: 0;
        }
        .alert-error span {
            font-size: 0.8125rem;
            color: #fca5a5;
        }

        /* Remember me */
        .remember-row {
            display: flex;
            align-items: center;
            margin-bottom: 1.75rem;
        }
        .remember-row input[type="checkbox"] {
            width: 18px; height: 18px;
            cursor: pointer;
            accent-color: #06b6d4;
            margin: 0;
            appearance: auto; /* Reset tailwind preflight */
            -webkit-appearance: checkbox; /* Ensure it shows up */
        }
        .remember-row label {
            font-size: 0.8125rem;
            color: #94a3b8;
            margin-left: 0.5rem;
            cursor: pointer;
        }

        /* Submit button */
        .btn-login {
            width: 100%;
            padding: 0.875rem;
            background: linear-gradient(135deg, #06b6d4, #3b82f6);
            border: none;
            border-radius: 12px;
            font-family: 'Inter', sans-serif;
            font-size: 0.9375rem;
            font-weight: 600;
            color: #fff;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: transform 0.15s, box-shadow 0.2s;
        }
        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(6, 182, 212, 0.35);
        }
        .btn-login:active {
            transform: translateY(0);
        }
        .btn-login::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, transparent 50%);
        }

        /* Footer */
        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.75rem;
            color: #475569;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-card {
                padding: 2rem 1.5rem;
                border-radius: 20px;
            }
            .brand h1 { font-size: 1.5rem; }
            .brand-icon { width: 60px; height: 60px; border-radius: 16px; }
            .brand-icon svg { width: 32px; height: 32px; }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Brand -->
        <div class="brand">
            <div class="brand-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.25c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V3a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282m0 0h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 0 1-2.649 7.521c-.388.482-.987.729-1.605.729H13.48c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 0 0-1.423-.23H5.904m7.594-4.5v-4.5m0 4.5H5.904a1.5 1.5 0 0 1-1.5-1.5v-3a1.5 1.5 0 0 1 1.5-1.5h1.73" />
                </svg>
            </div>
            <h1>Mochi Petshop</h1>
            <p>Sistem Informasi Penjualan & Stok Multi Cabang</p>
        </div>

        <!-- Card -->
        <div class="login-card">
            <h2>Login Sistem</h2>

            {{-- Session Status --}}
            @if (session('status'))
                <div class="alert-error" style="border-color: rgba(6,182,212,0.2); background: rgba(6,182,212,0.1);">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="color:#06b6d4;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <span style="color:#67e8f9;">{{ session('status') }}</span>
                </div>
            @endif

            {{-- General Error --}}
            @if ($errors->any())
                <div class="alert-error">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                    <span>Gagal masuk. Silakan periksa kembali email dan password Anda.</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email / Username -->
                <div class="form-group">
                    <label for="email">Email atau Username</label>
                    <div class="input-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                        </svg>
                        <input
                            id="email"
                            type="text"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="nama@email.com atau username"
                            required
                            autofocus
                            autocomplete="username"
                        >
                    </div>
                    @error('email')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                        >
                        <button type="button" class="toggle-password" onclick="togglePassword()" aria-label="Toggle password visibility" style="z-index: 20;">
                            <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            <svg id="eye-off-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="display:none;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="remember-row">
                    <input id="remember_me" type="checkbox" name="remember">
                    <label for="remember_me">Ingat saya</label>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-login" id="btn-login">
                    Masuk
                </button>
            </form>
        </div>

        <p class="login-footer">
            &copy; {{ date('Y') }} Mochi Petshop — Semua hak dilindungi
        </p>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeOffIcon = document.getElementById('eye-off-icon');

            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.style.display = 'none';
                eyeOffIcon.style.display = 'block';
            } else {
                input.type = 'password';
                eyeIcon.style.display = 'block';
                eyeOffIcon.style.display = 'none';
            }
        }
    </script>
</body>
</html>

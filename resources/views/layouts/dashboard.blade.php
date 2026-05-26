<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Mochi Petshop</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    <script>
        const savedTheme = localStorage.getItem('theme') || 'dark';
        if (savedTheme === 'light') {
            document.documentElement.setAttribute('data-theme', 'light');
        }
    </script>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --sidebar-w: 260px;
            --header-h: 64px;
            --bg-body: #0f172a;
            --bg-sidebar: #1e293b;
            --bg-card: rgba(30, 41, 59, 0.7);
            --bg-input: rgba(15, 23, 42, 0.6);
            --border-color: rgba(148, 163, 184, 0.1);
            --text-primary: #f1f5f9;
            --text-secondary: #cbd5e1;
            --text-muted: #94a3b8;
            --accent: #06b6d4;
            --accent-hover: #22d3ee;
            --accent-gradient: linear-gradient(135deg, #06b6d4, #3b82f6);
            --danger: #ef4444;
            --success: #22c55e;
            --warning: #f59e0b;
        }

        :root[data-theme="light"] {
            --bg-body: #f8fafc;
            --bg-sidebar: #ffffff;
            --bg-card: #ffffff;
            --bg-input: #f1f5f9;
            --border-color: #e2e8f0;
            --text-primary: #0f172a;
            --text-secondary: #334155;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-body);
            color: var(--text-primary);
            min-height: 100vh;
        }

        /* ─── Sidebar ───────────────────────────────── */
        .sidebar {
            position: fixed;
            top: 0; left: 0; bottom: 0;
            width: var(--sidebar-w);
            height: 100vh; /* Fallback */
            height: 100dvh;
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            z-index: 50;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-brand {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .sidebar-brand-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.9) !important;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            padding: 4px;
        }
        .sidebar-brand-icon svg { width: 22px; height: 22px; color: #fff; }
        .sidebar-brand-text h2 {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1.2;
        }
        .sidebar-brand-text p {
            font-size: 0.6875rem;
            color: var(--text-muted);
        }

        .sidebar-nav {
            flex: 1;
            padding: 1rem 0.75rem;
            overflow-y: auto;
        }
        .sidebar-nav-label {
            font-size: 0.6875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            padding: 0.5rem 0.75rem 0.375rem;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6875rem 0.75rem;
            border-radius: 10px;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.15s;
            margin-bottom: 2px;
        }
        .nav-item:hover {
            background: rgba(6, 182, 212, 0.08);
            color: var(--text-primary);
        }
        .nav-item.active {
            background: rgba(6, 182, 212, 0.12);
            color: var(--accent);
        }
        .nav-item svg { width: 20px; height: 20px; flex-shrink: 0; }

        .sidebar-footer {
            padding: 1rem 0.75rem;
            border-top: 1px solid var(--border-color);
        }
        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 0.75rem;
            margin-bottom: 0.5rem;
        }
        .sidebar-user-avatar {
            width: 36px; height: 36px;
            background: var(--accent-gradient);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.875rem;
            color: #fff;
            flex-shrink: 0;
        }
        .sidebar-user-info .user-name {
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--text-primary);
        }
        .sidebar-user-info .user-role {
            font-size: 0.6875rem;
            color: var(--text-muted);
            text-transform: capitalize;
        }

        .btn-logout {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            width: 100%;
            padding: 0.6875rem 0.75rem;
            border-radius: 10px;
            font-family: 'Inter', sans-serif;
            font-size: 0.875rem;
            font-weight: 500;
            color: #f87171;
            background: none;
            border: none;
            cursor: pointer;
            transition: background 0.15s;
        }
        .btn-logout:hover {
            background: rgba(239, 68, 68, 0.08);
        }
        .btn-logout svg { width: 20px; height: 20px; }

        /* ─── Main Content ──────────────────────────── */
        .main-content {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
        }

        .top-header {
            height: var(--header-h);
            background: var(--bg-sidebar);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            position: sticky;
            top: 0;
            z-index: 40;
        }
        .top-header h1 {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-primary);
        }
        .top-header-right {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .top-header-right .badge-role {
            font-size: 0.75rem;
            font-weight: 500;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            background: rgba(6, 182, 212, 0.12);
            color: var(--accent);
            text-transform: capitalize;
        }

        .notif-wrapper {
            position: relative;
        }
        .notif-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 0.5rem;
            width: 320px;
            background: var(--bg-card);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
            z-index: 50;
            max-height: 400px;
            overflow-y: auto;
        }
        .notif-dropdown.show {
            display: block;
        }
        .notif-item {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
        .notif-item:last-child {
            border-bottom: none;
        }
        .notif-item.danger {
            border-left: 4px solid var(--danger);
        }
        .notif-item.warning {
            border-left: 4px solid #f59e0b;
        }

        .hamburger {
            display: none;
            background: none;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 0.25rem;
        }
        .hamburger svg { width: 24px; height: 24px; }

        .content-body {
            padding: 1.5rem;
        }

        /* ─── Overlay (mobile) ──────────────────────── */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 45;
        }

        /* TomSelect Theme Overrides */
        .ts-control {
            background: var(--bg-input) !important;
            border: 1px solid var(--border-color) !important;
            color: var(--text-primary) !important;
            border-radius: 8px !important;
            padding: 0.6875rem 0.875rem !important;
            min-height: auto !important;
            box-shadow: none !important;
            display: flex;
            align-items: center;
        }
        .ts-control.focus {
            border-color: var(--accent) !important;
            box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.15) !important;
        }
        .ts-dropdown {
            background: var(--bg-body) !important;
            border: 1px solid var(--border-color) !important;
            color: var(--text-primary) !important;
            border-radius: 8px !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
            z-index: 9999 !important;
        }
        .ts-dropdown .option {
            padding: 0.6875rem 0.875rem !important;
            color: var(--text-primary) !important;
        }
        .ts-dropdown .option:hover, .ts-dropdown .active {
            background: var(--accent) !important;
            color: white !important;
        }
        .ts-control input {
            color: var(--text-primary) !important;
            font-size: 0.875rem !important;
        }
        .ts-control .item {
            color: var(--text-primary) !important;
        }
        .ts-wrapper.single .ts-control:after {
            border-color: var(--text-muted) transparent transparent transparent !important;
        }

        /* ─── Responsive ────────────────────────────── */
        @media (max-width: 768px) {
            body.sidebar-open {
                overflow: hidden;
            }
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .sidebar-overlay.open {
                display: block;
            }
            .main-content {
                margin-left: 0;
            }
            .hamburger {
                display: flex;
            }
            
            /* Mobile Spacing Optimizations */
            .top-header {
                padding: 0 1rem;
            }
            .content-body {
                padding: 1rem;
            }
            .card {
                padding: 1rem;
                border-radius: 12px;
            }
            .card-header {
                margin-bottom: 1rem;
            }
            .data-table th, .data-table td {
                padding: 0.75rem;
                font-size: 0.875rem;
            }
            .stat-card {
                padding: 1rem;
                gap: 0.75rem;
            }
            .stat-icon svg {
                width: 20px;
                height: 20px;
            }
            .stat-value {
                font-size: 1.25rem;
            }
        }

        /* ─── Utility Classes ───────────────────────── */
        .card {
            background: var(--bg-card);
            backdrop-filter: blur(16px);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.5rem;
        }
        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
        }
        .card-header h3 {
            font-size: 1rem;
            font-weight: 600;
        }

        .welcome-text {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }
        .welcome-sub {
            font-size: 0.9375rem;
            color: var(--text-secondary);
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }
        .stat-card {
            background: var(--bg-card);
            backdrop-filter: blur(16px);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .stat-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .stat-icon svg { width: 24px; height: 24px; color: #fff; }
        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1.2;
        }
        .stat-label {
            font-size: 0.8125rem;
            color: var(--text-muted);
        }

        /* ─── Table Styles ──────────────────────────── */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        .data-table th {
            text-align: left;
            padding: 0.75rem 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border-color);
        }
        .data-table td {
            padding: 0.875rem 1rem;
            font-size: 0.875rem;
            color: var(--text-secondary);
            border-bottom: 1px solid var(--border-color);
        }
        .data-table tbody tr {
            transition: background 0.1s;
        }
        .data-table tbody tr:hover {
            background: rgba(6, 182, 212, 0.04);
        }
        .data-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* ─── Pagination Laravel Style ──────────────────────────── */
        .pagination-container {
            margin-top: 1.5rem;
            display: flex;
            justify-content: flex-end;
        }
        .pagination {
            display: flex;
            list-style: none;
            gap: 0.25rem;
        }
        .pagination li a, .pagination li span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            padding: 0 0.5rem;
            border-radius: 8px;
            font-size: 0.8125rem;
            font-weight: 500;
            color: var(--text-secondary);
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            text-decoration: none;
            transition: all 0.15s;
        }
        .pagination li a:hover {
            background: rgba(6, 182, 212, 0.1);
            color: var(--accent);
            border-color: rgba(6, 182, 212, 0.3);
        }
        .pagination li.active span {
            background: var(--accent-gradient);
            color: #fff;
            border-color: transparent;
        }
        .pagination li.disabled span {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* ─── SweetAlert2 Custom Dark Theme ──────────────────────────── */
        .swal-custom-popup {
            background: var(--bg-card) !important;
            backdrop-filter: blur(16px) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 16px !important;
            color: var(--text-primary) !important;
            font-family: 'Inter', sans-serif !important;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5) !important;
        }
        .swal-custom-title {
            color: var(--text-primary) !important;
            font-size: 1.125rem !important;
            font-weight: 600 !important;
        }
        .swal-custom-content {
            color: var(--text-secondary) !important;
            font-size: 0.875rem !important;
        }
        .swal-custom-confirm {
            background: var(--accent-gradient) !important;
            border-radius: 10px !important;
            font-weight: 500 !important;
            padding: 0.625rem 1.25rem !important;
        }
        .swal-custom-cancel {
            background: rgba(148, 163, 184, 0.1) !important;
            color: var(--text-secondary) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 10px !important;
            font-weight: 500 !important;
            padding: 0.625rem 1.25rem !important;
        }
        .swal-custom-toast {
            background: var(--bg-card) !important;
            backdrop-filter: blur(8px) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 12px !important;
            color: var(--text-primary) !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
        }

        /* ─── Badges ────────────────────────────────── */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.625rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        .badge-owner {
            background: rgba(139, 92, 246, 0.12);
            color: #a78bfa;
        }
        .badge-kepala_cabang {
            background: rgba(6, 182, 212, 0.12);
            color: #22d3ee;
        }
        .badge-kasir {
            background: rgba(34, 197, 94, 0.12);
            color: #4ade80;
        }

        /* ─── Buttons ───────────────────────────────── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            border-radius: 10px;
            font-family: 'Inter', sans-serif;
            font-size: 0.875rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s;
        }
        .btn svg { width: 18px; height: 18px; }
        .btn-primary {
            background: var(--accent-gradient);
            color: #fff;
        }
        .btn-primary:hover {
            box-shadow: 0 4px 16px rgba(6, 182, 212, 0.3);
            transform: translateY(-1px);
        }
        .btn-secondary {
            background: rgba(148, 163, 184, 0.1);
            color: var(--text-secondary);
            border: 1px solid var(--border-color);
        }
        .btn-secondary:hover {
            background: rgba(148, 163, 184, 0.15);
            color: var(--text-primary);
        }
        .btn-sm {
            padding: 0.4375rem 0.875rem;
            font-size: 0.8125rem;
            border-radius: 8px;
        }

        /* ─── Form Styles ───────────────────────────── */
        .form-group {
            margin-bottom: 1.25rem;
        }
        .form-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }
        .form-control {
            width: 100%;
            padding: 0.6875rem 0.875rem;
            background: var(--bg-input);
            border: 1px solid rgba(148, 163, 184, 0.15);
            border-radius: 10px;
            font-family: 'Inter', sans-serif;
            font-size: 0.875rem;
            color: var(--text-primary);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-control::placeholder { color: var(--text-muted); }
        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.15);
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='2' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='m19.5 8.25-7.5 7.5-7.5-7.5'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px;
            padding-right: 2.5rem;
        }
        select.form-control option {
            background-color: var(--bg-body);
            color: var(--text-primary);
        }

        .form-error {
            font-size: 0.8125rem;
            color: var(--danger);
            margin-top: 0.375rem;
        }

        /* Responsive grid specifically for charts and POS */
        .chart-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
        }
        
        .pos-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        @media (max-width: 900px) {
            .chart-grid {
                grid-template-columns: 1fr;
            }
            .pos-grid {
                grid-template-columns: 1fr !important;
            }
            .card-header {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 0.75rem !important;
            }
            .card-header > div {
                width: 100%;
                justify-content: space-between;
            }
            .card-header form {
                width: 100%;
                flex-direction: column !important;
                align-items: stretch !important;
            }
            .card-header form input, .card-header form select, .card-header form a {
                width: 100% !important;
            }
            #search-product {
                width: 100% !important;
            }
        }

        @media (max-width: 640px) {
            .form-grid { grid-template-columns: 1fr; }
            .notif-dropdown {
                position: fixed;
                top: 70px;
                right: 1rem;
                left: 1rem;
                width: auto;
                max-width: none;
                margin-top: 0;
            }
            
            /* Responsive table rows for item inputs */
            .data-table:has(.item-row) thead {
                display: none;
            }
            .data-table:has(.item-row) tbody, .item-row {
                display: block;
                width: 100%;
            }
            .item-row {
                margin-bottom: 1rem;
                padding-bottom: 1rem;
                border-bottom: 2px dashed var(--border-color);
            }
            .item-row td {
                display: flex;
                flex-direction: column;
                padding: 0.5rem 0;
                border: none;
                width: 100%;
            }
            .item-row td select, .item-row td input {
                width: 100%;
                box-sizing: border-box;
            }
            .item-row td button {
                align-self: flex-end;
                margin-top: 0.5rem;
                background: rgba(239, 68, 68, 0.1) !important;
                padding: 0.5rem 1rem !important;
                border-radius: 8px;
            }
        }

        .form-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
            flex-wrap: wrap;
        }

        /* ─── Alert Messages ────────────────────────── */
        .alert {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1rem;
            border-radius: 12px;
            margin-bottom: 1.25rem;
            font-size: 0.875rem;
        }
        .alert svg { width: 20px; height: 20px; flex-shrink: 0; }
        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.2);
            color: #4ade80;
        }
            @media print {
            @page { size: landscape; margin: 10mm; }
            * { overflow: visible !important; }
            .sidebar, .top-header, .no-print { display: none !important; }
            .main-content { margin: 0 !important; padding: 0 !important; }
            .dashboard-container { display: block !important; }
            body { background-color: white !important; font-size: 11pt !important; }
            .card { box-shadow: none !important; border: none !important; margin: 0 !important; padding: 0 !important; }
            .data-table { width: 100% !important; table-layout: auto !important; }
            .data-table th, .data-table td { 
                border-color: #000 !important; 
                padding: 6px !important; 
                white-space: normal !important; 
                word-break: break-word !important;
                font-size: 10pt !important;
            }
            .card-header { padding: 0 0 1rem 0 !important; }
        }
    </style>
</head>
<body>
    <!-- Sidebar Overlay (mobile) -->
    <div class="sidebar-overlay" id="sidebar-overlay" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <!-- Brand -->
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon" style="background: transparent;">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width: 100%; height: 100%; object-fit: contain;">
            </div>
            <div class="sidebar-brand-text">
                <h2>Mochi Petshop</h2>
                <p>Sistem Informasi</p>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="sidebar-nav">
            <p class="sidebar-nav-label">Menu</p>

            {{-- Dashboard link based on role --}}
            @php
                $dashboardRoute = match(Auth::user()->role) {
                    'owner' => 'owner.dashboard',
                    'kepala_cabang' => 'kepala-cabang.dashboard',
                    'kasir' => 'kasir.dashboard',
                    default => 'login',
                };
            @endphp

            <a href="{{ route($dashboardRoute) }}" class="nav-item @if(request()->routeIs('*.dashboard')) active @endif">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
                Dashboard
            </a>

            {{-- Owner-only: Manajemen dan View Only --}}
            @if(Auth::user()->isOwner())
                <p class="sidebar-nav-label" style="margin-top: 1rem;">Manajemen</p>
                <a href="{{ route('users.index') }}" class="nav-item @if(request()->routeIs('users.*')) active @endif">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                    Data User
                </a>
                <a href="{{ route('branches.index') }}" class="nav-item @if(request()->routeIs('branches.*')) active @endif">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                    </svg>
                    Data Cabang
                </a>

                <p class="sidebar-nav-label" style="margin-top: 1rem;">Master Data</p>
                <a href="{{ route('categories.index') }}" class="nav-item @if(request()->routeIs('categories.*')) active @endif">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" /></svg>
                    Kategori
                </a>
                <a href="{{ route('units.index') }}" class="nav-item @if(request()->routeIs('units.*')) active @endif">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    Satuan
                </a>
                <a href="{{ route('suppliers.index') }}" class="nav-item @if(request()->routeIs('suppliers.*')) active @endif">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" /></svg>
                    Supplier
                </a>
                <a href="{{ route('products.index') }}" class="nav-item @if(request()->routeIs('products.*')) active @endif">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" /></svg>
                    Produk
                </a>

                <p class="sidebar-nav-label" style="margin-top: 1rem;">Operasional</p>
                <a href="{{ route('incoming-stocks.index') }}" class="nav-item @if(request()->routeIs('incoming-stocks.*')) active @endif">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" /></svg>
                    Stok Masuk
                </a>
                <a href="{{ route('outgoing-stocks.index') }}" class="nav-item @if(request()->routeIs('outgoing-stocks.*')) active @endif">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                    Stok Keluar
                </a>
                <a href="{{ route('stock-transfers.index') }}" class="nav-item @if(request()->routeIs('stock-transfers.*')) active @endif">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" /></svg>
                    Perpindahan Barang
                </a>
                <a href="{{ route('returns.index') }}" class="nav-item @if(request()->routeIs('returns.*')) active @endif">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
                    Retur Barang
                </a>
                <a href="{{ route('owner.discounts.index') }}" class="nav-item @if(request()->routeIs('owner.discounts.*')) active @endif">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" /></svg>
                    Persetujuan Diskon
                </a>

                <p class="sidebar-nav-label" style="margin-top: 1rem;">Laporan Analitik</p>
                <a href="{{ route('shifts.index') }}" class="nav-item @if(request()->routeIs('shifts.*')) active @endif">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    Manajemen Shift Kasir
                </a>
                <a href="{{ route('transactions.index') }}" class="nav-item @if(request()->routeIs('transactions.*')) active @endif">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" /></svg>
                    Data Transaksi
                </a>
                <a href="{{ route('reports.sales') }}" class="nav-item @if(request()->routeIs('reports.sales')) active @endif">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" /></svg>
                    Laporan Penjualan
                </a>
                <a href="{{ route('reports.best-sellers') }}" class="nav-item @if(request()->routeIs('reports.best-sellers')) active @endif">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" /></svg>
                    Produk Terlaris
                </a>
                <a href="{{ route('reports.stocks') }}" class="nav-item @if(request()->routeIs('reports.stocks')) active @endif">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Zm3.75 11.625a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg>
                    Laporan Stok
                </a>
            @endif

            {{-- Kepala Cabang: Master Data & Operasional --}}
            @if(Auth::user()->role === 'kepala_cabang')
                <p class="sidebar-nav-label" style="margin-top: 1rem;">Master Data</p>
                <a href="{{ route('kepala-cabang.categories.index') }}" class="nav-item @if(request()->routeIs('kepala-cabang.categories.*')) active @endif">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" /></svg>
                    Kategori
                </a>
                <a href="{{ route('kepala-cabang.units.index') }}" class="nav-item @if(request()->routeIs('kepala-cabang.units.*')) active @endif">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    Satuan
                </a>
                <a href="{{ route('kepala-cabang.suppliers.index') }}" class="nav-item @if(request()->routeIs('kepala-cabang.suppliers.*')) active @endif">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" /></svg>
                    Data Supplier
                </a>
                <a href="{{ route('kepala-cabang.products.index') }}" class="nav-item @if(request()->routeIs('kepala-cabang.products.*')) active @endif">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" /></svg>
                    Katalog Produk
                </a>
                
                <p class="sidebar-nav-label" style="margin-top: 1rem;">Operasional Toko</p>
                <a href="{{ route('kepala-cabang.stock-ins.index') }}" class="nav-item @if(request()->routeIs('kepala-cabang.stock-ins.*')) active @endif">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m9 13.5 3 3m0 0 3-3m-3 3v-6m1.06-4.19-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" /></svg>
                    Stok Masuk
                </a>
                <a href="{{ route('kepala-cabang.stock-outs.index') }}" class="nav-item @if(request()->routeIs('kepala-cabang.stock-outs.*')) active @endif">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m15 13.5-3-3m0 0-3 3m3-3v6m-3-11.25H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" /></svg>
                    Stok Keluar
                </a>
                <a href="{{ route('kepala-cabang.stock-transfers.index') }}" class="nav-item @if(request()->routeIs('kepala-cabang.stock-transfers.*')) active @endif">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" /></svg>
                    Mutasi Barang
                </a>
                <a href="{{ route('kepala-cabang.returns.index') }}" class="nav-item @if(request()->routeIs('kepala-cabang.returns.*')) active @endif">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
                    Persetujuan Retur
                </a>
                <a href="{{ route('kepala-cabang.discounts.index') }}" class="nav-item @if(request()->routeIs('kepala-cabang.discounts.*')) active @endif">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" /></svg>
                    Pengajuan Diskon
                </a>
                
                <p class="sidebar-nav-label" style="margin-top: 1rem;">Laporan Analitik</p>
                <a href="{{ route('kepala-cabang.shifts.index') }}" class="nav-item @if(request()->routeIs('kepala-cabang.shifts.*')) active @endif">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    Manajemen Shift Kasir
                </a>
                <a href="{{ route('kepala-cabang.transactions.index') }}" class="nav-item @if(request()->routeIs('kepala-cabang.transactions.*')) active @endif">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" /></svg>
                    Data Transaksi
                </a>
                <a href="{{ route('kepala-cabang.reports.sales') }}" class="nav-item @if(request()->routeIs('kepala-cabang.reports.sales')) active @endif">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" /></svg>
                    Laporan Penjualan
                </a>
                <a href="{{ route('kepala-cabang.reports.best-sellers') }}" class="nav-item @if(request()->routeIs('kepala-cabang.reports.best-sellers')) active @endif">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" /></svg>
                    Produk Terlaris
                </a>
                <a href="{{ route('kepala-cabang.reports.stocks') }}" class="nav-item @if(request()->routeIs('kepala-cabang.reports.stocks')) active @endif">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Zm3.75 11.625a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg>
                    Laporan Stok
                </a>
            @endif
            {{-- Kasir: POS & Riwayat --}}
            @if(Auth::user()->role === 'kasir')
                <p class="sidebar-nav-label" style="margin-top: 1rem;">Point of Sales</p>
                <a href="{{ route('kasir.transactions.create') }}" class="nav-item @if(request()->routeIs('kasir.transactions.create')) active @endif">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" /></svg>
                    Kasir Baru
                </a>
                <a href="{{ route('kasir.transactions.index') }}" class="nav-item @if(request()->routeIs('kasir.transactions.index')) active @endif">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    Riwayat Transaksi
                </a>
                <a href="{{ route('kasir.returns.index') }}" class="nav-item @if(request()->routeIs('kasir.returns.*')) active @endif">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
                    Retur Barang
                </a>

                <p class="sidebar-nav-label" style="margin-top: 1rem;">Master Data</p>
                <a href="{{ route('kasir.categories.index') }}" class="nav-item @if(request()->routeIs('kasir.categories.*')) active @endif">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" /></svg>
                    Kategori
                </a>
                <a href="{{ route('kasir.units.index') }}" class="nav-item @if(request()->routeIs('kasir.units.*')) active @endif">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    Satuan
                </a>
                <a href="{{ route('kasir.suppliers.index') }}" class="nav-item @if(request()->routeIs('kasir.suppliers.*')) active @endif">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" /></svg>
                    Supplier
                </a>
                <a href="{{ route('kasir.products.index') }}" class="nav-item @if(request()->routeIs('kasir.products.*')) active @endif">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" /></svg>
                    Produk
                </a>
            @endif
        </nav>

        <!-- Footer / User -->
        <div class="sidebar-footer">
            <a href="{{ route('profile.edit') }}" class="sidebar-user" style="text-decoration: none; transition: background 0.2s; border-radius: 10px;" onmouseover="this.style.backgroundColor='rgba(139, 92, 246, 0.05)'" onmouseout="this.style.backgroundColor='transparent'">
                <div class="sidebar-user-avatar" style="overflow: hidden; padding: 0; background: transparent;">
                    @if(Auth::user()->photo_profile)
                        <img src="{{ Storage::url(Auth::user()->photo_profile) }}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: var(--accent-gradient); color: white;">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div class="sidebar-user-info">
                    <div class="user-name">{{ Auth::user()->name }}</div>
                    <div class="user-role">{{ str_replace('_', ' ', Auth::user()->role) }}</div>
                </div>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Header -->
        <header class="top-header">
            <div style="display:flex;align-items:center;gap:0.75rem;">
                <button class="hamburger" onclick="toggleSidebar()" aria-label="Toggle sidebar">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
                <h1>@yield('page-title', 'Dashboard')</h1>
            </div>
            <div class="top-header-right">
                @if(in_array(Auth::user()->role, ['owner', 'kepala_cabang']))
                    <div class="notif-wrapper">
                        <button onclick="toggleNotif()" class="btn btn-sm" style="background: transparent; border: 1px solid var(--border-color); color: var(--text-primary); display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 50%; padding: 0; position: relative;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 18px; height: 18px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                            </svg>
                            @php
                                $lowStockCount = isset($lowStockNotifications) ? $lowStockNotifications->count() : 0;
                                $pendingReturnCount = isset($pendingReturnNotifications) ? $pendingReturnNotifications->count() : 0;
                                $totalNotifs = $lowStockCount + $pendingReturnCount;
                            @endphp
                            @if($totalNotifs > 0)
                                <span style="position: absolute; top: -2px; right: -2px; background: var(--danger); width: 10px; height: 10px; border-radius: 50%; border: 2px solid var(--bg-sidebar);"></span>
                            @endif
                        </button>
                        
                        <div id="notif-dropdown" class="notif-dropdown">
                            <!-- Retur Section -->
                            @if($pendingReturnCount > 0)
                                <div style="padding: 1rem; border-bottom: 1px solid var(--border-color); font-weight: 600; color: var(--text-primary); background: rgba(239, 68, 68, 0.05);">
                                    Permintaan Retur ({{ $pendingReturnCount }})
                                </div>
                                <div>
                                    @foreach($pendingReturnNotifications as $notif)
                                        <a href="{{ Auth::user()->role === 'kepala_cabang' ? route('kepala-cabang.returns.index') : '#' }}" 
                                           class="notif-item danger"
                                           style="text-decoration: none; transition: background 0.2s;"
                                           onmouseover="this.style.backgroundColor='rgba(139, 92, 246, 0.05)'"
                                           onmouseout="this.style.backgroundColor='transparent'">
                                            <div style="font-weight: 600; font-size: 0.875rem; color: var(--text-primary);">{{ $notif->product->name ?? 'Produk Dihapus' }}</div>
                                            <div style="font-size: 0.8rem; color: var(--text-secondary);">
                                                Kasir: {{ $notif->user->name ?? '-' }}
                                            </div>
                                            <div style="font-size: 0.85rem; margin-top: 0.25rem;">
                                                <span style="color: var(--danger); font-weight: 600;">
                                                    Jml Retur: {{ $notif->quantity }}
                                                </span> 
                                                <span style="color: var(--text-muted); font-size: 0.75rem;">(Alasan: {{ Str::limit($notif->reason, 20) }})</span>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Stok Section -->
                            <div style="padding: 1rem; border-bottom: 1px solid var(--border-color); font-weight: 600; color: var(--text-primary);">
                                Peringatan Stok ({{ $lowStockCount }})
                            </div>
                            <div>
                                @if($lowStockCount > 0)
                                    @foreach($lowStockNotifications as $notif)
                                        <a href="{{ Auth::user()->role === 'kepala_cabang' ? route('kepala-cabang.stock-ins.create', ['product_id' => $notif->product_id]) : '#' }}" 
                                           class="notif-item {{ $notif->quantity == 0 ? 'danger' : 'warning' }}"
                                           style="text-decoration: none; transition: background 0.2s;"
                                           onmouseover="this.style.backgroundColor='rgba(139, 92, 246, 0.05)'"
                                           onmouseout="this.style.backgroundColor='transparent'">
                                            <div style="font-weight: 600; font-size: 0.875rem; color: var(--text-primary);">{{ $notif->product->name }}</div>
                                            <div style="font-size: 0.8rem; color: var(--text-secondary);">
                                                Cabang: {{ $notif->branch->name }}
                                            </div>
                                            <div style="font-size: 0.85rem; margin-top: 0.25rem;">
                                                <span style="color: {{ $notif->quantity == 0 ? 'var(--danger)' : '#f59e0b' }}; font-weight: 600;">
                                                    Sisa: {{ $notif->quantity }}
                                                </span> 
                                                <span style="color: var(--text-muted); font-size: 0.75rem;">(Min: {{ $notif->product->minimum_stock }})</span>
                                            </div>
                                        </a>
                                    @endforeach
                                @else
                                    <div style="padding: 1.5rem 1rem; text-align: center; color: var(--text-muted); font-size: 0.875rem;">
                                        Semua stok dalam kondisi aman.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
                
                <button onclick="toggleTheme()" class="btn btn-sm" style="background: transparent; border: 1px solid var(--border-color); color: var(--text-primary); display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 50%; padding: 0;">
                    <!-- Sun Icon -->
                    <svg id="icon-sun" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 18px; height: 18px; display: none;">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                    </svg>
                    <!-- Moon Icon -->
                    <svg id="icon-moon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 18px; height: 18px; display: none;">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                    </svg>
                </button>
                <span class="badge-role">{{ str_replace('_', ' ', Auth::user()->role) }}</span>
            </div>
        </header>

        <!-- Content -->
        <div class="content-body">
            @yield('content')
        </div>
    </div>

    <script>
        function toggleNotif() {
            const dropdown = document.getElementById('notif-dropdown');
            if(dropdown) {
                dropdown.classList.toggle('show');
            }
        }
        
        function toggleProfileDropdown() {
            const dropdown = document.getElementById('profile-dropdown');
            if(dropdown) {
                dropdown.classList.toggle('show');
            }
        }
        
        // Close dropdowns if clicked outside
        window.addEventListener('click', function(e) {
            if (!e.target.closest('.notif-wrapper')) {
                const notifs = document.querySelectorAll('.notif-dropdown');
                notifs.forEach(function(d) {
                    if (d.classList.contains('show')) d.classList.remove('show');
                });
            }
        });

        function toggleTheme() {
            const isLight = document.documentElement.getAttribute('data-theme') === 'light';
            if (isLight) {
                document.documentElement.removeAttribute('data-theme');
                localStorage.setItem('theme', 'dark');
            } else {
                document.documentElement.setAttribute('data-theme', 'light');
                localStorage.setItem('theme', 'light');
            }
            updateThemeIcon();
        }

        function updateThemeIcon() {
            const isLight = document.documentElement.getAttribute('data-theme') === 'light';
            document.getElementById('icon-sun').style.display = isLight ? 'block' : 'none';
            document.getElementById('icon-moon').style.display = isLight ? 'none' : 'block';
        }

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
            document.getElementById('sidebar-overlay').classList.toggle('open');
            document.body.classList.toggle('sidebar-open');
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateThemeIcon();

            // Konfigurasi default SweetAlert untuk menyatu dengan Dark Theme
            window.swalDark = Swal.mixin({
                customClass: {
                    popup: 'swal-custom-popup',
                    title: 'swal-custom-title',
                    htmlContainer: 'swal-custom-content',
                    confirmButton: 'swal-custom-confirm',
                    cancelButton: 'swal-custom-cancel'
                },
                buttonsStyling: false
            });

            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    toast: true,
                    position: window.innerWidth < 768 ? 'top' : 'bottom-end',
                    showConfirmButton: false,
                    timer: 3000,
                    customClass: {
                        popup: 'swal-custom-toast',
                        title: 'swal-custom-title'
                    }
                });
            @endif

            @if(session('error'))
                window.swalDark.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: '{{ session('error') }}',
                    showConfirmButton: true
                });
            @endif

            const deleteForms = document.querySelectorAll('.form-delete');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    window.swalDark.fire({
                        title: 'Apakah Anda Yakin?',
                        text: "Data yang dihapus tidak dapat dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            });
        });

        // Initialize TomSelect for searchable dropdowns
        function initSearchableSelects() {
            document.querySelectorAll('.searchable-select').forEach(el => {
                if (!el.tomselect) {
                    new TomSelect(el, {
                        create: false
                    });
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            initSearchableSelects();
        });
    </script>
</body>
</html>


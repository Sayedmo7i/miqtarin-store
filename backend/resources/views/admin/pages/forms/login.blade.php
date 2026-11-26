<!DOCTYPE html>
@php
    use App\Services\MediaService;
    $isRtl = session()->get('is_rtl') == 1;
    $locale = session()->get('locale') ?? ($isRtl ? 'ar' : 'en');
@endphp
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}" data-theme="light">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <script>
            (function() {
                try {
                    var t = localStorage.getItem('muqtarin_admin_theme');
                    if (t === 'dark' || t === 'light') {
                        document.documentElement.setAttribute('data-theme', t);
                    }
                } catch (e) {}
            })();
        </script>
        @if ($system_settings != null && !empty($system_settings['favicon']))
            <link rel="icon" type="image/png"
                href="{{ app(MediaService::class)->getMediaImageUrl($system_settings['favicon']) }}">
        @else
            <link rel="icon" type="image/svg+xml" href="{{ asset('assets/brand/icon.svg') }}">
        @endif
        <title>{{ labels('panel_labels.login', 'Login') }} | {{ $system_settings['app_name'] ?? 'MUQTARIN Store' }}</title>
        <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/brand/icon.svg') }}">

        <!-- MUQTARIN Brand Fonts: Tajawal (Arabic) + Inter (English) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet" />

        <link rel="stylesheet" href="{{ asset('assets/admin/css/iziToast.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/admin/css/sweetalert2.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/boxicons/css/boxicons.min.css') }}">

        <style>
            @property --beam-angle {
                syntax: '<angle>';
                initial-value: 0deg;
                inherits: false;
            }

            :root {
                --pine: #0F4C3A;
                --pine-deep: #07291E;
                --pine-hover: #0A3528;
                --pine-light: #E6F2ED;
                --emerald: #22C55E;
                --emerald-dark: #16A34A;
                --gold: #F6C667;
                --ink: #1F2937;
                --muted: #64748B;
                --canvas: #F8FAFC;
                --surface: #FFFFFF;
                --surface-alt: #F1F5F9;
                --input-bg: #F8FAFC;
                --border: #E2E8F0;
                --dot-color: rgba(15, 76, 58, 0.16);
                --shadow-shell: 0 24px 60px -12px rgba(15, 23, 42, 0.14), 0 8px 24px -8px rgba(15, 76, 58, 0.08);
            }

            [data-theme="dark"] {
                --ink: #F1F5F9;
                --muted: #94A3B8;
                --canvas: #0B0F14;
                --surface: #131920;
                --surface-alt: #1A222B;
                --input-bg: #0D1218;
                --border: #2A333E;
                --pine-light: rgba(34, 197, 94, 0.14);
                --dot-color: rgba(34, 197, 94, 0.16);
                --shadow-shell: 0 28px 70px -12px rgba(0, 0, 0, 0.65);
            }

            *, *::before, *::after { box-sizing: border-box; }

            body {
                margin: 0;
                min-height: 100vh;
                background-color: var(--canvas);
                color: var(--ink);
                font-family: 'Tajawal', 'Inter', sans-serif;
                position: relative;
                overflow-x: hidden;
                transition: background-color 0.25s ease, color 0.25s ease;
            }
            [dir="ltr"] body {
                font-family: 'Inter', 'Tajawal', sans-serif;
            }

            /* ── MagicUI DotPattern + Aceternity Ambient Glow ───────────────── */
            .muqtarin-dot-backdrop {
                position: fixed;
                inset: 0;
                z-index: 0;
                pointer-events: none;
                background-image: radial-gradient(var(--dot-color) 1.2px, transparent 1.2px);
                background-size: 22px 22px;
                -webkit-mask-image: radial-gradient(ellipse 75% 70% at 50% 45%, #000 40%, transparent 100%);
                mask-image: radial-gradient(ellipse 75% 70% at 50% 45%, #000 40%, transparent 100%);
            }
            .muqtarin-ambient-glow {
                position: fixed;
                inset: 0;
                z-index: 0;
                pointer-events: none;
                background:
                    radial-gradient(620px circle at 18% 20%, rgba(15, 76, 58, 0.12), transparent 60%),
                    radial-gradient(520px circle at 82% 80%, rgba(34, 197, 94, 0.09), transparent 58%);
            }

            /* ── Topbar Floating Controls ───────────────────────────────────── */
            .login-topbar {
                position: fixed;
                top: 18px;
                inset-inline-end: 22px;
                z-index: 30;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .theme-toggle-btn {
                height: 40px;
                padding: 0 14px;
                border-radius: 9999px;
                border: 1px solid var(--border);
                background: var(--surface);
                color: var(--ink);
                display: inline-flex;
                align-items: center;
                gap: 8px;
                cursor: pointer;
                font-size: 0.82rem;
                font-weight: 600;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
                transition: all 0.2s ease;
            }
            .theme-toggle-btn:hover {
                border-color: var(--pine);
                color: var(--pine);
                transform: translateY(-1px);
            }
            [data-theme="dark"] .theme-toggle-btn:hover {
                border-color: var(--emerald);
                color: var(--emerald);
            }
            .theme-toggle-btn svg {
                width: 16px;
                height: 16px;
            }

            /* ── Main Layout Container ──────────────────────────────────────── */
            .login-wrapper {
                position: relative;
                z-index: 5;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                padding: 32px 16px;
            }

            /* ── MagicUI BorderBeam Frame ───────────────────────────────────── */
            .login-beam-shell {
                position: relative;
                width: 100%;
                max-width: 920px;
                border-radius: 20px;
                padding: 1.5px;
                background: var(--border);
                box-shadow: var(--shadow-shell);
                overflow: hidden;
            }
            .login-beam-shell::before {
                content: '';
                position: absolute;
                inset: -40%;
                background: conic-gradient(
                    from var(--beam-angle),
                    transparent 0deg,
                    transparent 250deg,
                    var(--pine) 295deg,
                    var(--emerald) 330deg,
                    var(--gold) 348deg,
                    transparent 360deg
                );
                animation: muqtarin-beam-spin 7s linear infinite;
                z-index: 0;
                opacity: 0.9;
            }
            @keyframes muqtarin-beam-spin {
                to { --beam-angle: 360deg; }
            }

            .login-split-card {
                position: relative;
                z-index: 1;
                display: grid;
                grid-template-columns: 1fr;
                border-radius: 18.5px;
                overflow: hidden;
                background: var(--surface);
            }
            @media (min-width: 900px) {
                .login-split-card {
                    grid-template-columns: 1.02fr 1fr;
                }
            }

            /* ── Left/Start Showcase Panel (Aceternity / 21st.dev) ──────────── */
            .login-brand-panel {
                position: relative;
                display: none;
                flex-direction: column;
                justify-content: space-between;
                padding: 40px 36px;
                background: linear-gradient(155deg, #0F4C3A 0%, #0A3528 52%, #062119 100%);
                color: #FFFFFF;
                overflow: hidden;
            }
            @media (min-width: 900px) {
                .login-brand-panel {
                    display: flex;
                }
            }
            .login-brand-panel::before {
                content: '';
                position: absolute;
                inset: 0;
                background-image:
                    linear-gradient(to right, rgba(255, 255, 255, 0.04) 1px, transparent 1px),
                    linear-gradient(to bottom, rgba(255, 255, 255, 0.04) 1px, transparent 1px);
                background-size: 28px 28px;
                -webkit-mask-image: radial-gradient(circle at 30% 25%, #000 15%, transparent 80%);
                mask-image: radial-gradient(circle at 30% 25%, #000 15%, transparent 80%);
                pointer-events: none;
            }
            .login-brand-spotlight {
                position: absolute;
                top: -90px;
                inset-inline-end: -90px;
                width: 280px;
                height: 280px;
                border-radius: 50%;
                background: radial-gradient(circle, rgba(34, 197, 94, 0.28) 0%, rgba(246, 198, 103, 0.08) 45%, transparent 72%);
                pointer-events: none;
            }
            .login-brand-spotlight-bottom {
                position: absolute;
                bottom: -100px;
                inset-inline-start: -80px;
                width: 260px;
                height: 260px;
                border-radius: 50%;
                background: radial-gradient(circle, rgba(246, 198, 103, 0.18) 0%, transparent 70%);
                pointer-events: none;
            }

            .brand-panel-top {
                position: relative;
                z-index: 2;
            }
            .brand-pill {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 6px 12px;
                border-radius: 9999px;
                background: rgba(255, 255, 255, 0.08);
                border: 1px solid rgba(255, 255, 255, 0.14);
                font-size: 0.75rem;
                font-weight: 600;
                color: #E2F5EC;
                backdrop-filter: blur(8px);
                margin-bottom: 24px;
            }
            .brand-pill-dot {
                width: 7px;
                height: 7px;
                border-radius: 50%;
                background: var(--emerald);
                box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.25);
            }

            .brand-panel-logo {
                height: 44px;
                width: auto;
                object-fit: contain;
                margin-bottom: 18px;
            }
            .brand-tagline {
                font-size: 1.35rem;
                font-weight: 700;
                line-height: 1.45;
                color: #FFFFFF;
                margin: 0 0 10px;
            }
            .brand-subtext {
                font-size: 0.875rem;
                line-height: 1.6;
                color: rgba(255, 255, 255, 0.72);
                margin: 0;
            }

            /* Decorative Glassmorphic Console Preview (Pure Visual Craft) */
            .brand-preview-widget {
                position: relative;
                z-index: 2;
                margin-top: 28px;
                padding: 18px;
                border-radius: 14px;
                background: rgba(255, 255, 255, 0.07);
                border: 1px solid rgba(255, 255, 255, 0.14);
                backdrop-filter: blur(12px);
            }
            .preview-widget-bar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 14px;
            }
            .preview-dots {
                display: flex;
                gap: 6px;
            }
            .preview-dots span {
                width: 8px;
                height: 8px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.28);
            }
            .preview-dots span:first-child { background: var(--gold); }
            .preview-dots span:nth-child(2) { background: var(--emerald); }
            .preview-tag {
                font-size: 0.7rem;
                font-weight: 700;
                color: var(--gold);
                background: rgba(246, 198, 103, 0.14);
                border: 1px solid rgba(246, 198, 103, 0.28);
                padding: 2px 8px;
                border-radius: 6px;
                font-family: 'Inter', sans-serif;
            }
            .preview-bars {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 10px;
            }
            .preview-stat-box {
                padding: 10px;
                border-radius: 10px;
                background: rgba(0, 0, 0, 0.18);
                border: 1px solid rgba(255, 255, 255, 0.08);
            }
            .preview-stat-line {
                height: 6px;
                border-radius: 999px;
                background: rgba(255, 255, 255, 0.2);
                margin-bottom: 8px;
                width: 60%;
            }
            .preview-stat-val {
                height: 10px;
                border-radius: 999px;
                background: linear-gradient(90deg, var(--emerald), #86EFAC);
                width: 85%;
            }
            .preview-stat-box:nth-child(2) .preview-stat-val {
                background: linear-gradient(90deg, var(--gold), #FDE68A);
                width: 72%;
            }
            .preview-stat-box:nth-child(3) .preview-stat-val {
                background: linear-gradient(90deg, #FFFFFF, rgba(255,255,255,0.65));
                width: 90%;
            }

            /* ── Right/End Form Panel (OriginUI + shadcn + Uiverse) ─────────── */
            .login-form-panel {
                padding: 36px 28px;
                background: var(--surface);
                display: flex;
                flex-direction: column;
                justify-content: center;
                transition: background-color 0.25s ease;
            }
            @media (min-width: 576px) {
                .login-form-panel {
                    padding: 44px 40px;
                }
            }

            .mobile-brand-logo {
                display: flex;
                align-items: center;
                justify-content: center;
                height: 46px;
                margin-bottom: 20px;
            }
            @media (min-width: 900px) {
                .mobile-brand-logo {
                    display: none;
                }
            }
            .mobile-brand-logo img {
                max-height: 100%;
                width: auto;
                object-fit: contain;
            }

            .logo-for-dark { display: none; }
            [data-theme="dark"] .logo-for-light { display: none; }
            [data-theme="dark"] .logo-for-dark { display: inline-block; }

            .form-header {
                margin-bottom: 28px;
            }
            .form-kicker {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                font-size: 0.72rem;
                font-weight: 700;
                letter-spacing: 0.06em;
                text-transform: uppercase;
                color: var(--pine);
                background: var(--pine-light);
                padding: 4px 10px;
                border-radius: 6px;
                margin-bottom: 12px;
            }
            [data-theme="dark"] .form-kicker {
                color: var(--emerald);
            }
            .login-title {
                font-size: 1.55rem;
                font-weight: 800;
                color: var(--ink);
                margin: 0 0 6px;
                line-height: 1.25;
            }
            .login-subtitle {
                font-size: 0.88rem;
                color: var(--muted);
                margin: 0;
                line-height: 1.5;
            }

            /* OriginUI-style Input Field */
            .form-label {
                font-size: 0.84rem;
                font-weight: 600;
                color: var(--ink);
                margin-bottom: 7px;
                display: flex;
                align-items: center;
                gap: 4px;
            }

            .muqtarin-input-group {
                display: flex;
                align-items: center;
                background: var(--input-bg);
                border: 1.5px solid var(--border);
                border-radius: 10px;
                overflow: hidden;
                transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
            }
            .muqtarin-input-group:hover {
                border-color: #CBD5E1;
            }
            [data-theme="dark"] .muqtarin-input-group:hover {
                border-color: #3E4A59;
            }
            .muqtarin-input-group:focus-within {
                border-color: var(--pine);
                box-shadow: 0 0 0 3.5px rgba(15, 76, 58, 0.14);
                background: var(--surface);
            }
            [data-theme="dark"] .muqtarin-input-group:focus-within {
                border-color: var(--emerald);
                box-shadow: 0 0 0 3.5px rgba(34, 197, 94, 0.16);
            }

            .muqtarin-input-group .input-icon {
                padding-inline: 13px 6px;
                color: var(--muted);
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                transition: color 0.2s ease;
            }
            .muqtarin-input-group:focus-within .input-icon {
                color: var(--pine);
            }
            [data-theme="dark"] .muqtarin-input-group:focus-within .input-icon {
                color: var(--emerald);
            }
            .muqtarin-input-group .input-icon svg {
                width: 18px;
                height: 18px;
            }

            .muqtarin-input-group .form-control {
                border: none !important;
                background: transparent !important;
                color: var(--ink) !important;
                box-shadow: none !important;
                min-height: 46px;
                font-size: 0.925rem;
                padding-inline: 6px 12px;
                width: 100%;
            }
            .muqtarin-input-group .form-control::placeholder {
                color: var(--muted);
                opacity: 0.65;
            }

            .password-toggle-btn {
                border: none;
                background: transparent;
                color: var(--muted);
                padding-inline: 12px;
                height: 46px;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                transition: color 0.2s ease;
            }
            .password-toggle-btn:hover { color: var(--ink); }
            .password-toggle-btn svg {
                width: 18px;
                height: 18px;
            }

            .form-check-input {
                width: 17px;
                height: 17px;
                border-radius: 5px;
                border: 1.5px solid var(--border);
                background-color: var(--input-bg);
                cursor: pointer;
            }
            .form-check-input:checked {
                background-color: var(--pine) !important;
                border-color: var(--pine) !important;
            }
            [data-theme="dark"] .form-check-input:checked {
                background-color: var(--emerald) !important;
                border-color: var(--emerald) !important;
            }

            .forgot-link {
                font-size: 0.83rem;
                font-weight: 600;
                color: var(--pine);
                text-decoration: none;
                transition: opacity 0.2s ease;
            }
            [data-theme="dark"] .forgot-link { color: var(--emerald); }
            .forgot-link:hover { text-decoration: underline; opacity: 0.9; }

            /* Uiverse.io Inspired Primary CTA Button */
            .login_button {
                position: relative;
                overflow: hidden;
                display: inline-flex !important;
                align-items: center;
                justify-content: center;
                gap: 10px;
                background: linear-gradient(135deg, var(--pine) 0%, #135E48 100%) !important;
                border: 1px solid rgba(255, 255, 255, 0.1) !important;
                border-radius: 10px !important;
                min-height: 48px;
                font-size: 0.96rem;
                font-weight: 700;
                color: #FFFFFF !important;
                box-shadow: 0 8px 20px -4px rgba(15, 76, 58, 0.36) !important;
                transition: transform 0.18s ease, box-shadow 0.22s ease, background 0.22s ease !important;
            }
            .login_button::after {
                content: '';
                position: absolute;
                top: 0;
                inset-inline-start: -120%;
                width: 60%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
                transform: skewX(-22deg);
                transition: inset-inline-start 0.65s ease;
                pointer-events: none;
            }
            .login_button:hover::after {
                inset-inline-start: 140%;
            }
            .login_button:hover, .login_button:focus {
                background: linear-gradient(135deg, var(--pine-hover) 0%, var(--pine) 100%) !important;
                transform: translateY(-1.5px);
                box-shadow: 0 12px 26px -4px rgba(15, 76, 58, 0.44) !important;
            }
            .login_button:active {
                transform: scale(0.99);
            }
            .login_button .cta-arrow {
                width: 18px;
                height: 18px;
                transition: transform 0.2s ease;
            }
            .login_button:hover .cta-arrow {
                transform: translateX(3px);
            }
            [dir="rtl"] .login_button .cta-arrow {
                transform: scaleX(-1);
            }
            [dir="rtl"] .login_button:hover .cta-arrow {
                transform: scaleX(-1) translateX(3px);
            }

            .login-footer {
                margin-top: 22px;
                font-size: 0.8rem;
                color: var(--muted);
                text-align: center;
            }
            .login-footer a {
                color: var(--pine);
                font-weight: 700;
                text-decoration: none;
            }
            [data-theme="dark"] .login-footer a { color: var(--emerald); }
        </style>
    </head>

    <body>
        <div class="muqtarin-dot-backdrop" aria-hidden="true"></div>
        <div class="muqtarin-ambient-glow" aria-hidden="true"></div>

        <div class="login-topbar">
            <button type="button" class="theme-toggle-btn" onclick="toggleLoginTheme()" title="Toggle Theme" aria-label="Toggle Theme">
                <span id="login_theme_icon_wrap"></span>
                <span id="login_theme_label">Theme</span>
            </button>
        </div>

        <div class="login-wrapper">
            <div class="login-beam-shell">
                <div class="login-split-card">
                    {{-- Brand Showcase Panel --}}
                    <aside class="login-brand-panel" aria-label="MUQTARIN Store Brand">
                        <div class="login-brand-spotlight" aria-hidden="true"></div>
                        <div class="login-brand-spotlight-bottom" aria-hidden="true"></div>

                        <div class="brand-panel-top">
                            <div class="brand-pill">
                                <span class="brand-pill-dot"></span>
                                <span>MUQTARIN Store • مقترن ستور</span>
                            </div>
                            <div>
                                <img src="{{ asset('assets/brand/logo-dark.svg') }}" alt="MUQTARIN Store" class="brand-panel-logo">
                            </div>
                            <h1 class="brand-tagline">
                                نربط الفكرة بالتنفيذ
                            </h1>
                            <p class="brand-subtext">
                                Connecting Ideas to Execution
                            </p>
                        </div>

                        <div class="brand-preview-widget" aria-hidden="true">
                            <div class="preview-widget-bar">
                                <div class="preview-dots">
                                    <span></span><span></span><span></span>
                                </div>
                                <span class="preview-tag">ADMIN WORKSPACE</span>
                            </div>
                            <div class="preview-bars">
                                <div class="preview-stat-box">
                                    <div class="preview-stat-line"></div>
                                    <div class="preview-stat-val"></div>
                                </div>
                                <div class="preview-stat-box">
                                    <div class="preview-stat-line"></div>
                                    <div class="preview-stat-val"></div>
                                </div>
                                <div class="preview-stat-box">
                                    <div class="preview-stat-line"></div>
                                    <div class="preview-stat-val"></div>
                                </div>
                            </div>
                        </div>
                    </aside>

                    {{-- Login Form Panel --}}
                    <main class="login-form-panel">
                        <div class="mobile-brand-logo">
                            <img src="{{ asset('assets/brand/logo.svg') }}" alt="MUQTARIN Store" class="logo-for-light">
                            <img src="{{ asset('assets/brand/logo-dark.svg') }}" alt="MUQTARIN Store" class="logo-for-dark">
                        </div>

                        <div class="form-header">
                            <span class="form-kicker">
                                {{ $system_settings['app_name'] ?? 'MUQTARIN Store' }}
                            </span>
                            <h2 class="login-title">
                                {{ labels('panel_labels.admin_login', 'Admin Login') }}
                            </h2>
                            <p class="login-subtitle">
                                {{ labels('panel_labels.enter_details_to_sign_in', 'Hey, enter your details to sign in to your account') }}
                            </p>
                        </div>

                        <form class="form_authentication" action="{{ route('admin.authenticate') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="login_input">
                                    <span>{{ labels('panel_labels.email_or_username', 'Email, Username or Mobile') }}</span>
                                    <span class='text-danger'>*</span>
                                </label>
                                <div class="muqtarin-input-group">
                                    <span class="input-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    </span>
                                    <input type="text" class="form-control" name="login" id="login_input"
                                        placeholder="admin@gmail.com / admin / 9876543210"
                                        value="admin@gmail.com" required autofocus>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="show_password">
                                    <span>{{ labels('panel_labels.password', 'Password') }}</span>
                                    <span class='text-danger'>*</span>
                                </label>
                                <div class="muqtarin-input-group">
                                    <span class="input-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                    </span>
                                    <input type="password" class="form-control copied_password" name="password"
                                        id="show_password"
                                        placeholder="••••••••"
                                        value="12345678" required>
                                    <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility()" aria-label="Toggle password">
                                        <span id="password_toggle_icon_wrap">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>
                                        </span>
                                    </button>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check d-flex align-items-center gap-2 mb-0">
                                    <input class="form-check-input mt-0" type="checkbox" id="rememberMe" checked>
                                    <label class="form-check-label small" style="color: var(--muted); cursor: pointer;" for="rememberMe">
                                        {{ labels('panel_labels.remember_me', 'Remember me') }}
                                    </label>
                                </div>
                                <a class="forgot-link" href="{{ route('password.request') }}">
                                    {{ labels('panel_labels.forgot_password', 'Forgot Password') }}?
                                </a>
                            </div>

                            <button type="submit" class="btn btn-lg login_button w-100">
                                <span>{{ labels('panel_labels.sign_in', 'Sign In') }}</span>
                                <svg class="cta-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                            </button>
                        </form>
                    </main>
                </div>
            </div>

            <div class="login-footer">
                {{ labels('panel_labels.copyright', 'Copyright') }} © {{ date('Y') }}
                <a href="{{ route('admin.home') }}">{{ $system_settings['app_name'] ?? 'MUQTARIN Store' }}</a>.
                {{ labels('panel_labels.all_rights_reserved', 'All rights reserved.') }}
            </div>
        </div>

        <!-- Core JS Files -->
        <script src="{{ asset('/assets/admin/js/jquery.min.js') }}"></script>
        <script src="{{ asset('/assets/js/core/popper.min.js') }}"></script>
        <script src="{{ asset('/assets/js/core/bootstrap.min.js') }}"></script>
        <script src="{{ asset('assets/admin/js/iziToast.min.js') }}"></script>
        <script src="{{ asset('assets/admin/js/sweetalert2.min.js') }}"></script>
        <script src="{{ asset('assets/admin/custom/custom.js') }}"></script>

        <script>
            var eyeOpenSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>';
            var eyeOffSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M10.733 5.076a10.744 10.744 0 0 1 11.205 6.575 1 1 0 0 1 0 .696 10.747 10.747 0 0 1-1.444 2.49"/><path d="M14.084 14.158a3 3 0 0 1-4.242-4.242"/><path d="M17.479 17.499a10.75 10.75 0 0 1-15.417-5.151 1 1 0 0 1 0-.696 10.75 10.75 0 0 1 4.446-5.143"/><path d="m2 2 20 20"/></svg>';
            var moonSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>';
            var sunSvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>';

            function togglePasswordVisibility() {
                var passwordInput = document.getElementById('show_password');
                var wrap = document.getElementById('password_toggle_icon_wrap');
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    if (wrap) wrap.innerHTML = eyeOffSvg;
                } else {
                    passwordInput.type = 'password';
                    if (wrap) wrap.innerHTML = eyeOpenSvg;
                }
            }

            function syncLoginThemeIcon() {
                var isDark = document.documentElement.getAttribute('data-theme') === 'dark';
                var wrap = document.getElementById('login_theme_icon_wrap');
                var label = document.getElementById('login_theme_label');
                if (wrap) wrap.innerHTML = isDark ? sunSvg : moonSvg;
                if (label) label.textContent = isDark ? 'Light' : 'Dark';
            }

            function toggleLoginTheme() {
                var html = document.documentElement;
                var next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                html.setAttribute('data-theme', next);
                try { localStorage.setItem('muqtarin_admin_theme', next); } catch (e) {}
                syncLoginThemeIcon();
            }

            syncLoginThemeIcon();
        </script>
    </body>
</html>

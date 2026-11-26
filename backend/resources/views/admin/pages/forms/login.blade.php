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
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />

        <link rel="stylesheet" href="{{ asset('assets/admin/css/iziToast.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/admin/css/sweetalert2.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/boxicons/css/boxicons.min.css') }}">

        <style>
            :root {
                --pine: #0F4C3A;
                --pine-hover: #0A3528;
                --pine-light: #E6F2ED;
                --emerald: #22C55E;
                --emerald-dark: #16A34A;
                --gold: #F6C667;
                --ink: #1F2937;
                --muted: #64748B;
                --canvas: #F8FAFC;
                --surface: #FFFFFF;
                --input-bg: #F8FAFC;
                --border: #E2E8F0;
                --shadow-card: 0 12px 32px rgba(15, 23, 42, 0.08);
            }
            [data-theme="dark"] {
                --ink: #F1F5F9;
                --muted: #94A3B8;
                --canvas: #0D1117;
                --surface: #161B22;
                --input-bg: #0D1117;
                --border: #30363D;
                --pine-light: rgba(34, 197, 94, 0.14);
                --shadow-card: 0 16px 40px rgba(0, 0, 0, 0.45);
            }

            *, *::before, *::after { box-sizing: border-box; }

            body {
                margin: 0;
                min-height: 100vh;
                background-color: var(--canvas);
                background-image:
                    radial-gradient(at 15% 15%, rgba(15, 76, 58, 0.08) 0px, transparent 50%),
                    radial-gradient(at 85% 85%, rgba(34, 197, 94, 0.07) 0px, transparent 50%);
                color: var(--ink);
                font-family: 'Tajawal', 'Inter', sans-serif;
                transition: background-color 0.2s ease, color 0.2s ease;
            }
            [dir="ltr"] body {
                font-family: 'Inter', 'Tajawal', sans-serif;
            }

            .login-topbar {
                position: fixed;
                top: 16px;
                inset-inline-end: 20px;
                z-index: 20;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .theme-toggle-btn {
                width: 40px;
                height: 40px;
                border-radius: 10px;
                border: 1px solid var(--border);
                background: var(--surface);
                color: var(--ink);
                display: inline-flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                font-size: 1.2rem;
                box-shadow: 0 2px 8px rgba(0,0,0,0.06);
                transition: all 0.2s ease;
            }
            .theme-toggle-btn:hover {
                border-color: var(--pine);
                color: var(--emerald-dark);
                transform: translateY(-1px);
            }

            .login-wrapper {
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                padding: 24px 16px;
            }

            .login-card {
                width: 100%;
                max-width: 430px;
                background: var(--surface);
                border: 1px solid var(--border);
                border-radius: 16px;
                box-shadow: var(--shadow-card);
                padding: 32px 28px;
                transition: background-color 0.2s ease, border-color 0.2s ease;
            }

            .login-brand-strip {
                height: 4px;
                width: 56px;
                border-radius: 999px;
                background: linear-gradient(90deg, var(--pine), var(--emerald));
                margin: 0 auto 18px;
            }

            .login-img-box {
                width: 100%;
                max-width: 210px;
                height: 56px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 16px;
            }
            .login-img-box img {
                max-width: 100%;
                max-height: 100%;
                object-fit: contain;
            }
            .logo-for-dark { display: none; }
            [data-theme="dark"] .logo-for-light { display: none; }
            [data-theme="dark"] .logo-for-dark { display: inline-block; }

            .login-title {
                font-size: 1.4rem;
                font-weight: 700;
                color: var(--ink);
                margin-bottom: 4px;
            }
            .login-subtitle {
                font-size: 0.875rem;
                color: var(--muted);
                margin-bottom: 0;
            }

            .form-label {
                font-size: 0.85rem;
                font-weight: 600;
                color: var(--ink);
                margin-bottom: 6px;
                display: block;
            }

            .muqtarin-input-group {
                display: flex;
                align-items: center;
                background: var(--input-bg);
                border: 1px solid var(--border);
                border-radius: 10px;
                overflow: hidden;
                transition: border-color 0.2s ease, box-shadow 0.2s ease;
            }
            .muqtarin-input-group:focus-within {
                border-color: var(--pine);
                box-shadow: 0 0 0 3px rgba(15, 76, 58, 0.14);
                background: var(--surface);
            }
            [data-theme="dark"] .muqtarin-input-group:focus-within {
                border-color: var(--emerald);
                box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.16);
            }

            .muqtarin-input-group .input-icon {
                padding: 0 12px;
                color: var(--muted);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.15rem;
            }

            .muqtarin-input-group .form-control {
                border: none !important;
                background: transparent !important;
                color: var(--ink) !important;
                box-shadow: none !important;
                min-height: 44px;
                font-size: 0.925rem;
                padding-inline: 4px 12px;
                width: 100%;
            }
            .muqtarin-input-group .form-control::placeholder {
                color: var(--muted);
                opacity: 0.7;
            }

            .password-toggle-btn {
                border: none;
                background: transparent;
                color: var(--muted);
                padding: 0 12px;
                cursor: pointer;
                display: flex;
                align-items: center;
                font-size: 1.15rem;
            }
            .password-toggle-btn:hover { color: var(--ink); }

            .form-check-input {
                border-color: var(--border);
                background-color: var(--input-bg);
            }
            .form-check-input:checked {
                background-color: var(--pine) !important;
                border-color: var(--pine) !important;
            }

            .forgot-link {
                font-size: 0.825rem;
                font-weight: 600;
                color: var(--pine);
                text-decoration: none;
            }
            [data-theme="dark"] .forgot-link { color: var(--emerald); }
            .forgot-link:hover { text-decoration: underline; }

            .login_button {
                background: var(--pine) !important;
                border: none !important;
                border-radius: 10px !important;
                min-height: 46px;
                font-size: 0.95rem;
                font-weight: 700;
                color: #FFFFFF !important;
                box-shadow: 0 6px 18px rgba(15, 76, 58, 0.24) !important;
                transition: background-color 0.2s ease, transform 0.15s ease !important;
            }
            .login_button:hover, .login_button:focus {
                background: var(--pine-hover) !important;
                transform: translateY(-1px);
            }

            .login-footer {
                margin-top: 24px;
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
        <div class="login-topbar">
            <button type="button" class="theme-toggle-btn" onclick="toggleLoginTheme()" title="Toggle Theme" aria-label="Toggle Theme">
                <i class='bx bx-moon' id="login_theme_icon"></i>
            </button>
        </div>

        <div class="login-wrapper">
            <div class="login-card">
                <div class="login-brand-strip"></div>
                <div class="text-center mb-4">
                    <div class="login-img-box">
                        <img src="{{ asset('assets/brand/logo.svg') }}" alt="MUQTARIN Store" class="logo-for-light">
                        <img src="{{ asset('assets/brand/logo-dark.svg') }}" alt="MUQTARIN Store" class="logo-for-dark">
                    </div>
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
                            {{ labels('panel_labels.email_or_username', 'Email, Username or Mobile') }}
                            <span class='text-danger'>*</span>
                        </label>
                        <div class="muqtarin-input-group">
                            <span class="input-icon"><i class='bx bx-user'></i></span>
                            <input type="text" class="form-control" name="login" id="login_input"
                                placeholder="admin@gmail.com / admin / 9876543210"
                                value="admin@gmail.com" required autofocus>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="show_password">
                            {{ labels('panel_labels.password', 'Password') }}
                            <span class='text-danger'>*</span>
                        </label>
                        <div class="muqtarin-input-group">
                            <span class="input-icon"><i class='bx bx-lock-alt'></i></span>
                            <input type="password" class="form-control copied_password" name="password"
                                id="show_password"
                                placeholder="••••••••"
                                value="12345678" required>
                            <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility()" aria-label="Toggle password">
                                <i class='bx bx-show' id="password_toggle_icon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="rememberMe" checked>
                            <label class="form-check-label small" style="color: var(--muted);" for="rememberMe">
                                {{ labels('panel_labels.remember_me', 'Remember me') }}
                            </label>
                        </div>
                        <a class="forgot-link" href="{{ route('password.request') }}">
                            {{ labels('panel_labels.forgot_password', 'Forgot Password') }}?
                        </a>
                    </div>

                    <button type="submit" class="btn btn-lg login_button w-100">
                        {{ labels('panel_labels.sign_in', 'Sign In') }}
                    </button>
                </form>
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
            function togglePasswordVisibility() {
                var passwordInput = document.getElementById('show_password');
                var icon = document.getElementById('password_toggle_icon');
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('bx-show');
                    icon.classList.add('bx-hide');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('bx-hide');
                    icon.classList.add('bx-show');
                }
            }

            function syncLoginThemeIcon() {
                var isDark = document.documentElement.getAttribute('data-theme') === 'dark';
                var icon = document.getElementById('login_theme_icon');
                if (icon) {
                    icon.className = isDark ? 'bx bx-sun' : 'bx bx-moon';
                }
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

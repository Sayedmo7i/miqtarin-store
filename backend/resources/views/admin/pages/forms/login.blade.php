<!DOCTYPE html>
<html lang="en">
    @php
        use App\Services\MediaService;
    @endphp

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @if ($system_settings != null && !empty($system_settings['favicon']))
            <link rel="icon" type="image/png"
                href="{{ app(MediaService::class)->getMediaImageUrl($system_settings['favicon']) }}">
        @endif
        <title>{{ labels('panel_labels.login', 'Login') }} | {{ $system_settings['app_name'] ?? 'eShop Plus' }}</title>
        <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/img/apple-icon.png') }}">
        <!-- Fonts and icons -->
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />

        <link rel="stylesheet" href="{{ asset('assets/admin/css/iziToast.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/admin/css/dropzone.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap-table.min.css') }}">

        <!-- CSS Files -->
        <link id="pagestyle" href="{{ asset('/assets/css/argon-dashboard.css?v=2.0.4') }}" rel="stylesheet" />
        <link rel="stylesheet" href="{{ asset('/assets/admin/css/select2.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('/assets/admin/css/tagify.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('/assets/admin/css/sweetalert2.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('/assets/admin/css/style.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/boxicons/css/boxicons.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/admin/custom/custom.css') }}">

        <style>
            body {
                background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                min-height: 100vh;
            }
            .login-wrapper {
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                padding: 20px;
            }
            .login-card {
                width: 100%;
                max-width: 440px;
                border-radius: 16px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08) !important;
                border: none;
                background: #ffffff;
            }
            .login-img-box {
                width: 100%;
                max-width: 220px;
                height: 70px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 15px auto;
            }
            .login-img-box img {
                max-width: 100%;
                max-height: 100%;
                object-fit: contain;
            }
            .login_button {
                background-color: #0F4C3A !important;
                border-color: #0F4C3A !important;
                border-radius: 8px !important;
                box-shadow: 0 4px 12px rgba(15, 76, 58, 0.25) !important;
                font-weight: 600;
            }
            .login_button:hover {
                background-color: #0A3528 !important;
            }
            .input-group-text {
                background-color: #f8f9fa;
                border-color: #e9ecef;
            }
            .form-control {
                border-color: #e9ecef;
            }
            .form-control:focus {
                border-color: #0F4C3A;
                box-shadow: 0 0 0 2px rgba(15, 76, 58, 0.15);
            }
        </style>
    </head>

    <body>
        <div class="login-wrapper">
            <div class="card login-card p-4">
                <div class="card-body p-2">
                    <div class="text-center mb-4">
                        <div class="login-img-box">
                            @php
                                $store_logo =
                                    !empty($system_settings['logo']) &&
                                    file_exists(public_path(config('constants.MEDIA_PATH') . $system_settings['logo']))
                                        ? app(MediaService::class)->getMediaImageUrl($system_settings['logo'])
                                        : asset('assets/img/default_full_logo.png');
                            @endphp
                            <img src="{{ $store_logo }}" alt="logo">
                        </div>
                        <h2 class="font-weight-bolder text-dark mb-1">
                            {{ labels('panel_labels.admin_login', 'Admin Login') }}
                        </h2>
                        <p class="text-muted text-sm mb-0">
                            {{ labels('panel_labels.enter_details_to_sign_in', 'Hey, enter your details to sign in to your account') }}
                        </p>
                    </div>

                    <form class="form_authentication" action="{{ route('admin.authenticate') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label text-sm font-weight-bold" for="login_input">
                                {{ labels('panel_labels.email_or_username', 'Email, Username or Mobile') }}
                                <span class='text-danger'>*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class='bx bx-user fs-5 text-muted'></i></span>
                                <input type="text" class="form-control" name="login" id="login_input"
                                    placeholder="admin@gmail.com / admin / 9876543210"
                                    value="admin@gmail.com" required autofocus>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label text-sm font-weight-bold" for="show_password">
                                {{ labels('panel_labels.password', 'Password') }}
                                <span class='text-danger'>*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class='bx bx-lock fs-5 text-muted'></i></span>
                                <input type="password" class="form-control copied_password" name="password"
                                    id="show_password"
                                    placeholder="••••••••"
                                    value="12345678" required>
                                <span class="input-group-text" style="cursor: pointer;" onclick="togglePasswordVisibility()">
                                    <i class='bx bx-show fs-5 text-muted' id="password_toggle_icon"></i>
                                </span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="rememberMe" checked>
                                <label class="form-check-label text-sm text-muted" for="rememberMe">
                                    {{ labels('panel_labels.remember_me', 'Remember me') }}
                                </label>
                            </div>
                            <a class="text-sm font-weight-bold text-primary" href="{{ route('password.request') }}">
                                {{ labels('panel_labels.forgot_password', 'Forgot Password') }}?
                            </a>
                        </div>

                        <button type="submit" class="btn btn-lg login_button w-100 text-white mb-2">
                            {{ labels('panel_labels.sign_in', 'Sign In') }}
                        </button>
                    </form>
                </div>
            </div>

            <div class="text-center text-muted text-sm mt-4">
                {{ labels('panel_labels.copyright', 'Copyright') }} © {{ date('Y') }}
                <a href="{{ route('admin.home') }}" class="text-muted font-weight-bold">{{ $system_settings['app_name'] ?? 'eShop Plus' }}</a>.
                {{ labels('panel_labels.all_rights_reserved', 'All rights reserved.') }}
            </div>
        </div>

        <!-- Core JS Files -->
        <script src="{{ asset('/assets/admin/js/jquery.min.js') }}"></script>
        <script src="{{ asset('/assets/js/core/popper.min.js') }}"></script>
        <script src="{{ asset('/assets/js/core/bootstrap.min.js') }}"></script>
        <script src="{{ asset('/assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
        <script src="{{ asset('/assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
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
        </script>
    </body>
</html>

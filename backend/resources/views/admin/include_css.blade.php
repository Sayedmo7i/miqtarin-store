@php
    use App\Services\MediaService;
@endphp

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        (function() {
            var root = document.documentElement;
            root.classList.add('mq-page-loading');
            try {
                var t = localStorage.getItem('muqtarin_admin_theme');
                if (t === 'dark' || t === 'light') {
                    root.setAttribute('data-theme', t);
                }
            } catch (e) {}
            window.__mqSkeletonGuard = setTimeout(function() {
                root.classList.remove('mq-page-loading', 'mq-nav-loading');
            }, 3500);
        })();
    </script>
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/brand/icon.svg') }}">
    <link rel="icon" type="image/png"
        href="{{ !empty($system_settings['favicon']) ? app(MediaService::class)->getMediaImageUrl($system_settings['favicon']) : asset('assets/brand/icon.svg') }}">
    <title>@yield('title') | {{ $system_settings['app_name'] ?? 'MUQTARIN Store' }}</title>

    <!-- MUQTARIN Brand Fonts: Tajawal (Arabic) + Inter (English) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <!-- filepond Css -->
    <link rel="stylesheet" href="{{ asset('frontend/elegant/css/intlTelInput.css') }}" />
    <link href="/assets/filepond/dist/filepond.css" rel="stylesheet" type="text/css" />
    <link href="/assets/filepond/dist/filepond-plugin-image-preview.css" rel="stylesheet" type="text/css" />
    <link href="/assets/filepond/dist/filepond-plugin-pdf-preview.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/filepond/dist/filepond-plugin-media-preview.css" rel="stylesheet" type="text/css" />
    <link href="/assets/filepond/dist/filepond-plugin-media-preview.min.css" rel="stylesheet" type="text/css" />

    <!-- Icons -->
    <link href="{{ asset('/assets/css/nucleo-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('/assets/css/nucleo-svg.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/admin/css/iziToast.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/dropzone.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/all.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/boxicons/css/boxicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap-table.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/daterangepicker.css') }}">

    <!-- Stepper & Plugins CSS -->
    <link href="{{ asset('/assets/admin/css/nouislider.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('/assets/css/jquery.rateyo.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('/assets/admin/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('/assets/admin/css/tagify.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('/assets/admin/css/sweetalert2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('/assets/admin/css/style.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('/assets/admin/css/lightbox.min.css') }}" rel="stylesheet" />

    <link id="pagestyle" rel="stylesheet" href="{{ asset('/assets/css/theme.min.css') }}">
    @yield('styles')
    @stack('styles')
    {{-- MUQTARIN Store Admin Design System --}}
    <link rel="stylesheet"
        href="{{ asset('assets/admin/custom/muqtarin-admin.css') }}?v={{ filemtime(public_path('assets/admin/custom/muqtarin-admin.css')) }}">
</head>

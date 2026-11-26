@php
    $isRtl = session()->get('is_rtl') == 1;
    $locale = session()->get('locale') ?? ($isRtl ? 'ar' : 'en');
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}" data-theme="light">

@include('admin.include_css')

<body>
    <script>
        (function() {
            try {
                if (sessionStorage.getItem('muqtarin_sidebar_collapsed') === '1') {
                    document.body.classList.add('sidebar-collapsed');
                }
            } catch (e) {}
        })();
    </script>
    <div id="db-wrapper">

        <x-admin.side-bar />
        <div id="page-content">

            <x-admin.header />
            <div class="container-fluid mt-5 px-6" {{ $isRtl ? 'dir=rtl' : '' }}>

                @yield('content')
            </div>
        </div>
    </div>
    <x-admin.footer />
    <!-- Scripts -->
    @include('admin.include_script')
</body>

</html>

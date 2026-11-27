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
    <div id="muqtarin-top-loader" aria-hidden="true">
        <div class="mq-top-loader-bar"></div>
    </div>
    <div id="muqtarin-page-skeleton" aria-hidden="true">
        <div class="mq-sk-hero">
            <div class="mq-sk-hero-text">
                <div class="mq-sk-line is-sm mq-sk-shimmer"></div>
                <div class="mq-sk-line is-lg mq-sk-shimmer"></div>
                <div class="mq-sk-line is-md mq-sk-shimmer" style="width: 260px;"></div>
            </div>
            <div class="mq-sk-hero-actions">
                <div class="mq-sk-pill mq-sk-shimmer"></div>
                <div class="mq-sk-pill mq-sk-shimmer" style="width: 138px;"></div>
                <div class="mq-sk-pill mq-sk-shimmer" style="width: 112px;"></div>
            </div>
        </div>

        <div class="mq-sk-chip-row">
            @for ($i = 0; $i < 4; $i++)
                <div class="mq-sk-chip">
                    <div class="d-flex flex-column gap-2 flex-grow-1">
                        <div class="mq-sk-line is-sm mq-sk-shimmer"></div>
                        <div class="mq-sk-line is-md mq-sk-shimmer" style="width: 64px;"></div>
                    </div>
                    <div class="mq-sk-icon mq-sk-shimmer" style="width: 34px; height: 34px;"></div>
                </div>
            @endfor
        </div>

        <div class="mq-sk-kpi-grid">
            @for ($i = 0; $i < 4; $i++)
                <div class="mq-sk-kpi-card">
                    <div class="d-flex flex-column gap-2 flex-grow-1">
                        <div class="mq-sk-line is-sm mq-sk-shimmer"></div>
                        <div class="mq-sk-line is-lg mq-sk-shimmer" style="width: 96px;"></div>
                    </div>
                    <div class="mq-sk-icon mq-sk-shimmer"></div>
                </div>
            @endfor
        </div>

        <div class="mq-sk-main-grid">
            <div class="mq-sk-card d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center justify-content-between gap-2">
                    <div class="mq-sk-line is-md mq-sk-shimmer"></div>
                    <div class="mq-sk-pill mq-sk-shimmer" style="height: 28px; width: 140px;"></div>
                </div>
                <div class="mq-sk-chart-bars">
                    <div class="mq-sk-bar mq-sk-shimmer" style="height: 44%;"></div>
                    <div class="mq-sk-bar mq-sk-shimmer" style="height: 72%;"></div>
                    <div class="mq-sk-bar mq-sk-shimmer" style="height: 55%;"></div>
                    <div class="mq-sk-bar mq-sk-shimmer" style="height: 88%;"></div>
                    <div class="mq-sk-bar mq-sk-shimmer" style="height: 62%;"></div>
                    <div class="mq-sk-bar mq-sk-shimmer" style="height: 94%;"></div>
                    <div class="mq-sk-bar mq-sk-shimmer" style="height: 68%;"></div>
                    <div class="mq-sk-bar mq-sk-shimmer" style="height: 80%;"></div>
                </div>
            </div>
            <div class="mq-sk-card">
                <div class="d-flex align-items-center justify-content-between gap-2">
                    <div class="mq-sk-line is-md mq-sk-shimmer" style="width: 130px;"></div>
                    <div class="mq-sk-line is-sm mq-sk-shimmer" style="width: 48px;"></div>
                </div>
                <div class="mq-sk-list">
                    @for ($i = 0; $i < 4; $i++)
                        <div class="mq-sk-list-row">
                            <div class="mq-sk-avatar mq-sk-shimmer"></div>
                            <div class="d-flex flex-column gap-2 flex-grow-1">
                                <div class="mq-sk-line is-md mq-sk-shimmer" style="width: {{ 62 + ($i % 3) * 12 }}%;"></div>
                                <div class="mq-sk-line is-sm mq-sk-shimmer" style="width: {{ 40 + ($i % 2) * 18 }}%;"></div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
    <div id="db-wrapper">

        <x-admin.side-bar />
        <div id="page-content">

            <x-admin.header />
            <div class="container-fluid mt-5 px-6 muqtarin-page-body" {{ $isRtl ? 'dir=rtl' : '' }}>

                @yield('content')
            </div>
        </div>
    </div>
    <x-admin.footer />
    <!-- Scripts -->
    @include('admin.include_script')
    @yield('scripts')
</body>

</html>

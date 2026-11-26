{{-- Shared Showcase Bridge for Admin Live Web Previews --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@600;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/boxicons/css/boxicons.min.css') }}?v={{ $version ?? time() }}">
<link rel="stylesheet" href="{{ asset('frontend/elegant/css/muqtarin-storefront.css') }}?v={{ $version ?? time() }}">

<style>
    html, body {
        margin: 0;
        padding: 0;
        min-height: 100%;
        background: var(--mq-canvas, #F8FAFC);
        color: var(--mq-ink, #0F172A);
        font-family: 'Inter', 'Tajawal', sans-serif;
        transition: background-color 180ms ease, color 180ms ease;
    }
    html[dir="rtl"] body {
        font-family: 'Tajawal', 'Inter', sans-serif;
    }
    html[data-theme="dark"],
    html[data-theme="dark"] body {
        background: #0B1217 !important;
        color: #F1F5F9 !important;
        --mq-canvas: #0B1217;
        --mq-surface: #121C24;
        --mq-surface-alt: #182530;
        --mq-ink: #F1F5F9;
        --mq-ink-soft: #CBD5E1;
        --mq-muted: #94A3B8;
        --mq-border: #253441;
    }
    html[data-theme="dark"] .product-box,
    html[data-theme="dark"] .card,
    html[data-theme="dark"] .table,
    html[data-theme="dark"] .order-table td,
    html[data-theme="dark"] .order-table th,
    html[data-theme="dark"] .bg-white {
        background-color: #121C24 !important;
        color: #F1F5F9 !important;
        border-color: #253441 !important;
    }
    html[data-theme="dark"] .text-secondary,
    html[data-theme="dark"] .text-muted {
        color: #94A3B8 !important;
    }
    .mq-showcase-stage {
        padding: 18px;
        max-width: 1120px;
        margin: 0 auto;
    }
    .mq-legacy-frame {
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid var(--mq-border, #E2E8F0);
        background: var(--mq-surface, #FFFFFF);
        box-shadow: 0 12px 30px -12px rgba(15, 23, 42, 0.14);
    }
    .mq-legacy-frame-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 16px;
        border-bottom: 1px solid var(--mq-border, #E2E8F0);
        background: var(--mq-surface-alt, #F8FAFC);
    }
    .mq-legacy-frame-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 700;
        font-size: 0.9rem;
        color: var(--mq-ink, #0F172A);
    }
    .mq-legacy-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 700;
        background: rgba(15, 76, 58, 0.1);
        color: var(--mq-pine, #0F4C3A);
    }
    html[data-theme="dark"] .mq-legacy-pill {
        background: rgba(34, 197, 94, 0.14);
        color: #4ADE80;
    }
    .mq-legacy-frame img {
        display: block;
        width: 100%;
        height: auto;
        max-height: 490px;
        object-fit: cover;
        object-position: top center;
    }
</style>

<script>
    (function () {
        function applyShowcasePayload(payload) {
            if (!payload) return;
            var styleId = typeof payload === 'string' ? payload : payload.style;
            var dir = typeof payload === 'object' ? payload.dir : null;
            var theme = typeof payload === 'object' ? payload.theme : null;
            var primaryColor = typeof payload === 'object' ? payload.primaryColor : null;

            if (dir === 'rtl' || dir === 'ltr') {
                document.documentElement.setAttribute('dir', dir);
                document.documentElement.setAttribute('lang', dir === 'rtl' ? 'ar' : 'en');
                if (document.body) {
                    document.body.setAttribute('dir', dir);
                }
                document.querySelectorAll('[data-en][data-ar]').forEach(function (el) {
                    el.textContent = dir === 'rtl' ? el.getAttribute('data-ar') : el.getAttribute('data-en');
                });
            }

            if (theme === 'dark' || theme === 'light') {
                document.documentElement.setAttribute('data-theme', theme);
                if (document.body) {
                    document.body.setAttribute('data-theme', theme);
                }
            }

            if (primaryColor && /^#[0-9A-Fa-f]{6}$/.test(primaryColor)) {
                document.documentElement.style.setProperty('--mq-pine', primaryColor);
                document.documentElement.style.setProperty('--primary-color', primaryColor);
            }

            if (styleId) {
                document.querySelectorAll('.content-section').forEach(function (section) {
                    section.classList.add('d-none');
                });
                var target = document.getElementById(styleId);
                if (target) {
                    target.classList.remove('d-none');
                }
            }
        }

        window.addEventListener('message', function (event) {
            applyShowcasePayload(event.data);
        });

        window.addEventListener('DOMContentLoaded', function () {
            try {
                if (window.parent && window.parent !== window) {
                    window.parent.postMessage({ showcaseReady: true }, '*');
                }
            } catch (e) {}
        });
    })();
</script>

@php
    $iframeId = $iframeId ?? 'web_preview_iframe';
    $iframeSrc = $iframeSrc ?? '';
    $badge = $badge ?? null;
    $height = $height ?? '420px';
@endphp
<div class="muqtarin-preview-frame mq-preview-frame-shell" data-preview-target="{{ $iframeId }}" data-dir="ltr" data-theme="light" data-viewport="desktop">
    <div class="muqtarin-preview-frame-bar mq-preview-toolbar">
        <div class="d-flex align-items-center gap-2">
            <span class="muqtarin-preview-dots"><span></span><span></span><span></span></span>
            <span class="muqtarin-preview-badge">{{ $badge ?? labels('admin_labels.web_live_preview', 'Web Preview') }}</span>
        </div>
        <div class="mq-preview-controls">
            <div class="mq-preview-seg" role="group" aria-label="{{ labels('admin_labels.viewport_size', 'Viewport') }}">
                <button type="button" class="mq-preview-ctrl is-active" data-preview-action="viewport" data-value="desktop"
                    title="{{ labels('admin_labels.desktop_view', 'Desktop') }}">
                    <i class="bx bx-desktop"></i>
                </button>
                <button type="button" class="mq-preview-ctrl" data-preview-action="viewport" data-value="tablet"
                    title="{{ labels('admin_labels.tablet_view', 'Tablet') }}">
                    <i class="bx bx-tab"></i>
                </button>
                <button type="button" class="mq-preview-ctrl" data-preview-action="viewport" data-value="mobile"
                    title="{{ labels('admin_labels.mobile_view', 'Mobile') }}">
                    <i class="bx bx-mobile-alt"></i>
                </button>
            </div>
            <div class="mq-preview-seg" role="group" aria-label="{{ labels('admin_labels.direction', 'Direction') }}">
                <button type="button" class="mq-preview-ctrl is-active" data-preview-action="dir" data-value="ltr">LTR</button>
                <button type="button" class="mq-preview-ctrl" data-preview-action="dir" data-value="rtl">RTL</button>
            </div>
            <div class="mq-preview-seg" role="group" aria-label="{{ labels('admin_labels.surface_theme', 'Surface') }}">
                <button type="button" class="mq-preview-ctrl is-active" data-preview-action="theme" data-value="light"
                    title="{{ labels('admin_labels.light_mode', 'Light') }}">
                    <i class="bx bx-sun"></i>
                </button>
                <button type="button" class="mq-preview-ctrl" data-preview-action="theme" data-value="dark"
                    title="{{ labels('admin_labels.dark_mode', 'Dark') }}">
                    <i class="bx bx-moon"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="mq-preview-viewport-stage">
        <iframe id="{{ $iframeId }}" src="{{ $iframeSrc }}" style="height: {{ $height }};" loading="lazy"></iframe>
    </div>
</div>

/* =============================================================================
   MUQTARIN Store — Modern Toast Notification System (Sonner / OriginUI / MagicUI)
   Upgrades iziToast globally with clean stacked cards, SVG icon badges,
   Dark/Light mode, RTL/LTR alignment, deduplication, and "Dismiss All" pill.
   ============================================================================= */
(function () {
  'use strict';

  if (window.__muqtarinToastInit) return;
  window.__muqtarinToastInit = true;

  var ICONS = {
    success: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>',
    error: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/></svg>',
    warning: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>',
    info: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>'
  };

  var CLOSE_SVG = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>';

  function injectStyles() {
    if (document.getElementById('muqtarin-toast-styles')) return;
    var style = document.createElement('style');
    style.id = 'muqtarin-toast-styles';
    style.textContent = [
      '/* Wrapper alignment: eliminate staircase widths */',
      '.iziToast-wrapper.iziToast-wrapper-topRight,',
      '.iziToast-wrapper.iziToast-wrapper-topLeft,',
      '.iziToast-wrapper.iziToast-wrapper-bottomRight,',
      '.iziToast-wrapper.iziToast-wrapper-bottomLeft {',
      '  width: 384px !important;',
      '  max-width: calc(100vw - 24px) !important;',
      '  padding: 14px !important;',
      '  display: flex !important;',
      '  flex-direction: column !important;',
      '  gap: 8px !important;',
      '  max-height: calc(100vh - 24px) !important;',
      '  overflow-y: auto !important;',
      '  scrollbar-width: none !important;',
      '  z-index: 100000 !important;',
      '}',
      '.iziToast-wrapper::-webkit-scrollbar { display: none; }',
      '.iziToast-wrapper .iziToast-capsule {',
      '  width: 100% !important;',
      '  margin: 0 !important;',
      '  display: block !important;',
      '}',
      '',
      '/* Core Toast Card (Sonner + OriginUI + MagicUI subtle glow) */',
      '.iziToast-wrapper .iziToast {',
      '  width: 100% !important;',
      '  min-height: 54px !important;',
      '  margin: 0 !important;',
      '  padding: 12px 14px !important;',
      '  padding-inline-end: 38px !important;',
      '  border-radius: 12px !important;',
      '  background: var(--surface, #FFFFFF) !important;',
      '  color: var(--ink, #1F2937) !important;',
      '  border: 1px solid var(--border, #E2E8F0) !important;',
      '  border-inline-start: 3.5px solid var(--pine, #0F4C3A) !important;',
      '  box-shadow: 0 12px 30px -8px rgba(15, 23, 42, 0.14), 0 4px 10px -4px rgba(15, 23, 42, 0.06) !important;',
      '  font-family: "Tajawal", "Inter", sans-serif !important;',
      '  position: relative !important;',
      '  overflow: hidden !important;',
      '  pointer-events: auto !important;',
      '  backdrop-filter: blur(12px) !important;',
      '  transition: transform 0.2s ease, opacity 0.2s ease, box-shadow 0.2s ease !important;',
      '}',
      '[dir="ltr"] .iziToast-wrapper .iziToast {',
      '  font-family: "Inter", "Tajawal", sans-serif !important;',
      '}',
      '[data-theme="dark"] .iziToast-wrapper .iziToast {',
      '  background: var(--surface, #161B22) !important;',
      '  color: var(--ink, #F1F5F9) !important;',
      '  border-color: var(--border, #30363D) !important;',
      '  box-shadow: 0 18px 36px -10px rgba(0, 0, 0, 0.65), 0 4px 12px rgba(0, 0, 0, 0.35) !important;',
      '}',
      '.iziToast-wrapper .iziToast::after {',
      '  display: none !important;',
      '}',
      '',
      '/* Semantic Colors & Corner Spotlight Glow */',
      '.iziToast-wrapper .iziToast.iziToast-color-green,',
      '.iziToast-wrapper .iziToast[data-muqtarin-type="success"] {',
      '  border-inline-start-color: #16A34A !important;',
      '  background-image: radial-gradient(220px circle at 0% 0%, rgba(34, 197, 94, 0.10), transparent 70%) !important;',
      '}',
      '.iziToast-wrapper .iziToast.iziToast-color-red,',
      '.iziToast-wrapper .iziToast.iziToast-error,',
      '.iziToast-wrapper .iziToast[data-muqtarin-type="error"] {',
      '  border-inline-start-color: #DC2626 !important;',
      '  background-image: radial-gradient(220px circle at 0% 0%, rgba(220, 38, 38, 0.11), transparent 70%) !important;',
      '}',
      '.iziToast-wrapper .iziToast.iziToast-color-orange,',
      '.iziToast-wrapper .iziToast.iziToast-color-yellow,',
      '.iziToast-wrapper .iziToast[data-muqtarin-type="warning"] {',
      '  border-inline-start-color: #D97706 !important;',
      '  background-image: radial-gradient(220px circle at 0% 0%, rgba(246, 198, 103, 0.16), transparent 70%) !important;',
      '}',
      '.iziToast-wrapper .iziToast.iziToast-color-blue,',
      '.iziToast-wrapper .iziToast[data-muqtarin-type="info"] {',
      '  border-inline-start-color: #2563EB !important;',
      '  background-image: radial-gradient(220px circle at 0% 0%, rgba(37, 99, 235, 0.10), transparent 70%) !important;',
      '}',
      '[dir="rtl"] .iziToast-wrapper .iziToast.iziToast-color-green,',
      '[dir="rtl"] .iziToast-wrapper .iziToast[data-muqtarin-type="success"] {',
      '  background-image: radial-gradient(220px circle at 100% 0%, rgba(34, 197, 94, 0.10), transparent 70%) !important;',
      '}',
      '[dir="rtl"] .iziToast-wrapper .iziToast.iziToast-color-red,',
      '[dir="rtl"] .iziToast-wrapper .iziToast.iziToast-error,',
      '[dir="rtl"] .iziToast-wrapper .iziToast[data-muqtarin-type="error"] {',
      '  background-image: radial-gradient(220px circle at 100% 0%, rgba(220, 38, 38, 0.11), transparent 70%) !important;',
      '}',
      '[dir="rtl"] .iziToast-wrapper .iziToast.iziToast-color-orange,',
      '[dir="rtl"] .iziToast-wrapper .iziToast.iziToast-color-yellow,',
      '[dir="rtl"] .iziToast-wrapper .iziToast[data-muqtarin-type="warning"] {',
      '  background-image: radial-gradient(220px circle at 100% 0%, rgba(246, 198, 103, 0.16), transparent 70%) !important;',
      '}',
      '[dir="rtl"] .iziToast-wrapper .iziToast.iziToast-color-blue,',
      '[dir="rtl"] .iziToast-wrapper .iziToast[data-muqtarin-type="info"] {',
      '  background-image: radial-gradient(220px circle at 100% 0%, rgba(37, 99, 235, 0.10), transparent 70%) !important;',
      '}',
      '.iziToast-wrapper .iziToast.iziToast-animateInside .iziToast-title,',
      '.iziToast-wrapper .iziToast.iziToast-animateInside .iziToast-message,',
      '.iziToast-wrapper .iziToast.iziToast-animateInside .iziToast-icon {',
      '  opacity: 1 !important;',
      '}',
      '',
      '/* Body Flex Layout (replaces float/margin hacks cleanly in LTR & RTL) */',
      '.iziToast-wrapper .iziToast > .iziToast-body {',
      '  display: flex !important;',
      '  align-items: flex-start !important;',
      '  gap: 11px !important;',
      '  margin: 0 !important;',
      '  padding: 0 !important;',
      '  min-height: 0 !important;',
      '  text-align: start !important;',
      '  width: 100% !important;',
      '}',
      '.iziToast-wrapper .iziToast > .iziToast-body::after { display: none !important; }',
      '',
      '/* Crisp Icon Pill Badge */',
      '.iziToast-wrapper .iziToast > .iziToast-body .iziToast-icon {',
      '  position: static !important;',
      '  top: auto !important;',
      '  left: auto !important;',
      '  right: auto !important;',
      '  margin: 0 !important;',
      '  width: 32px !important;',
      '  height: 32px !important;',
      '  min-width: 32px !important;',
      '  border-radius: 9px !important;',
      '  display: inline-flex !important;',
      '  align-items: center !important;',
      '  justify-content: center !important;',
      '  background-image: none !important;',
      '  background: rgba(15, 76, 58, 0.12) !important;',
      '  color: #0F4C3A !important;',
      '  flex-shrink: 0 !important;',
      '}',
      '.iziToast-wrapper .iziToast > .iziToast-body .iziToast-icon::before { display: none !important; content: none !important; }',
      '.iziToast-wrapper .iziToast > .iziToast-body .iziToast-icon svg { width: 17px; height: 17px; display: block; }',
      '',
      '.iziToast-wrapper .iziToast.iziToast-color-green .iziToast-icon,',
      '.iziToast-wrapper .iziToast[data-muqtarin-type="success"] .iziToast-icon {',
      '  background: rgba(22, 163, 74, 0.14) !important;',
      '  color: #16A34A !important;',
      '}',
      '.iziToast-wrapper .iziToast.iziToast-color-red .iziToast-icon,',
      '.iziToast-wrapper .iziToast.iziToast-error .iziToast-icon,',
      '.iziToast-wrapper .iziToast[data-muqtarin-type="error"] .iziToast-icon {',
      '  background: rgba(220, 38, 38, 0.14) !important;',
      '  color: #DC2626 !important;',
      '}',
      '.iziToast-wrapper .iziToast.iziToast-color-orange .iziToast-icon,',
      '.iziToast-wrapper .iziToast.iziToast-color-yellow .iziToast-icon,',
      '.iziToast-wrapper .iziToast[data-muqtarin-type="warning"] .iziToast-icon {',
      '  background: rgba(217, 119, 6, 0.16) !important;',
      '  color: #D97706 !important;',
      '}',
      '.iziToast-wrapper .iziToast.iziToast-color-blue .iziToast-icon,',
      '.iziToast-wrapper .iziToast[data-muqtarin-type="info"] .iziToast-icon {',
      '  background: rgba(37, 99, 235, 0.14) !important;',
      '  color: #2563EB !important;',
      '}',
      '[data-theme="dark"] .iziToast-wrapper .iziToast.iziToast-color-green .iziToast-icon,',
      '[data-theme="dark"] .iziToast-wrapper .iziToast[data-muqtarin-type="success"] .iziToast-icon { color: #4ADE80 !important; }',
      '[data-theme="dark"] .iziToast-wrapper .iziToast.iziToast-color-red .iziToast-icon,',
      '[data-theme="dark"] .iziToast-wrapper .iziToast.iziToast-error .iziToast-icon,',
      '[data-theme="dark"] .iziToast-wrapper .iziToast[data-muqtarin-type="error"] .iziToast-icon { color: #F87171 !important; }',
      '[data-theme="dark"] .iziToast-wrapper .iziToast.iziToast-color-orange .iziToast-icon,',
      '[data-theme="dark"] .iziToast-wrapper .iziToast.iziToast.iziToast-color-yellow .iziToast-icon,',
      '[data-theme="dark"] .iziToast-wrapper .iziToast[data-muqtarin-type="warning"] .iziToast-icon { color: #FBBF24 !important; }',
      '[data-theme="dark"] .iziToast-wrapper .iziToast.iziToast-color-blue .iziToast-icon,',
      '[data-theme="dark"] .iziToast-wrapper .iziToast[data-muqtarin-type="info"] .iziToast-icon { color: #60A5FA !important; }',
      '',
      '/* Texts Stack */',
      '.iziToast-wrapper .iziToast > .iziToast-body .iziToast-texts {',
      '  display: flex !important;',
      '  flex-direction: column !important;',
      '  gap: 2px !important;',
      '  float: none !important;',
      '  margin: 0 !important;',
      '  padding: 0 !important;',
      '  flex: 1 !important;',
      '  min-width: 0 !important;',
      '}',
      '.iziToast-wrapper .iziToast > .iziToast-body .iziToast-title {',
      '  float: none !important;',
      '  display: block !important;',
      '  margin: 0 !important;',
      '  padding: 0 !important;',
      '  font-size: 0.84rem !important;',
      '  font-weight: 700 !important;',
      '  line-height: 1.3 !important;',
      '  color: var(--ink, #1F2937) !important;',
      '  text-align: start !important;',
      '}',
      '[data-theme="dark"] .iziToast-wrapper .iziToast > .iziToast-body .iziToast-title {',
      '  color: #F8FAFC !important;',
      '}',
      '.iziToast-wrapper .iziToast > .iziToast-body .iziToast-message {',
      '  float: none !important;',
      '  display: block !important;',
      '  margin: 0 !important;',
      '  padding: 0 !important;',
      '  font-size: 0.81rem !important;',
      '  font-weight: 500 !important;',
      '  line-height: 1.45 !important;',
      '  color: var(--muted, #64748B) !important;',
      '  text-align: start !important;',
      '  white-space: normal !important;',
      '  word-break: break-word !important;',
      '}',
      '[data-theme="dark"] .iziToast-wrapper .iziToast > .iziToast-body .iziToast-message {',
      '  color: #CBD5E1 !important;',
      '}',
      '',
      '/* Close Button (OriginUI-clean rounded X) */',
      '.iziToast-wrapper .iziToast > .iziToast-close {',
      '  position: absolute !important;',
      '  top: 10px !important;',
      '  right: 10px !important;',
      '  left: auto !important;',
      '  width: 24px !important;',
      '  height: 24px !important;',
      '  border-radius: 6px !important;',
      '  background: transparent !important;',
      '  background-image: none !important;',
      '  color: var(--muted, #64748B) !important;',
      '  opacity: 0.75 !important;',
      '  display: inline-flex !important;',
      '  align-items: center !important;',
      '  justify-content: center !important;',
      '  transition: background-color 0.16s ease, color 0.16s ease, opacity 0.16s ease !important;',
      '}',
      '[dir="rtl"] .iziToast-wrapper .iziToast > .iziToast-close { left: 10px !important; right: auto !important; }',
      '.iziToast-wrapper .iziToast > .iziToast-close:hover {',
      '  opacity: 1 !important;',
      '  background: var(--surface-alt, #F1F5F9) !important;',
      '  color: var(--ink, #1F2937) !important;',
      '}',
      '[data-theme="dark"] .iziToast-wrapper .iziToast > .iziToast-close:hover {',
      '  background: rgba(255, 255, 255, 0.08) !important;',
      '  color: #FFFFFF !important;',
      '}',
      '.iziToast-wrapper .iziToast > .iziToast-close svg { width: 14px; height: 14px; display: block; }',
      '',
      '/* Progress Bar */',
      '.iziToast-wrapper .iziToast > .iziToast-progressbar {',
      '  height: 2.5px !important;',
      '  background: transparent !important;',
      '}',
      '.iziToast-wrapper .iziToast > .iziToast-progressbar > div {',
      '  height: 2.5px !important;',
      '  border-radius: 0 !important;',
      '  background: var(--pine, #0F4C3A) !important;',
      '}',
      '.iziToast-wrapper .iziToast.iziToast-color-green > .iziToast-progressbar > div,',
      '.iziToast-wrapper .iziToast[data-muqtarin-type="success"] > .iziToast-progressbar > div { background: #16A34A !important; }',
      '.iziToast-wrapper .iziToast.iziToast-color-red > .iziToast-progressbar > div,',
      '.iziToast-wrapper .iziToast.iziToast-error > .iziToast-progressbar > div,',
      '.iziToast-wrapper .iziToast[data-muqtarin-type="error"] > .iziToast-progressbar > div { background: #DC2626 !important; }',
      '.iziToast-wrapper .iziToast.iziToast-color-orange > .iziToast-progressbar > div,',
      '.iziToast-wrapper .iziToast.iziToast-color-yellow > .iziToast-progressbar > div,',
      '.iziToast-wrapper .iziToast[data-muqtarin-type="warning"] > .iziToast-progressbar > div { background: #D97706 !important; }',
      '.iziToast-wrapper .iziToast.iziToast-color-blue > .iziToast-progressbar > div,',
      '.iziToast-wrapper .iziToast[data-muqtarin-type="info"] > .iziToast-progressbar > div { background: #2563EB !important; }',
      '',
      '/* Dismiss All Pill (Appears automatically when >= 3 toasts stack) */',
      '.muqtarin-toast-clear-all {',
      '  align-self: flex-end;',
      '  pointer-events: auto;',
      '  display: inline-flex;',
      '  align-items: center;',
      '  gap: 6px;',
      '  padding: 5px 12px;',
      '  border-radius: 9999px;',
      '  font-size: 0.74rem;',
      '  font-weight: 700;',
      '  font-family: "Tajawal", "Inter", sans-serif;',
      '  background: var(--surface, #FFFFFF);',
      '  color: var(--ink, #1F2937);',
      '  border: 1px solid var(--border, #E2E8F0);',
      '  box-shadow: 0 6px 16px rgba(15, 23, 42, 0.14);',
      '  cursor: pointer;',
      '  transition: transform 0.16s ease, border-color 0.16s ease, color 0.16s ease;',
      '}',
      '.iziToast-wrapper-topLeft .muqtarin-toast-clear-all,',
      '.iziToast-wrapper-bottomLeft .muqtarin-toast-clear-all {',
      '  align-self: flex-start;',
      '}',
      '.muqtarin-toast-clear-all svg { width: 12px; height: 12px; display: block; flex-shrink: 0; }',
      '[data-theme="dark"] .muqtarin-toast-clear-all {',
      '  background: #1C232D;',
      '  color: #F1F5F9;',
      '  border-color: #30363D;',
      '}',
      '.muqtarin-toast-clear-all:hover {',
      '  border-color: #DC2626;',
      '  color: #DC2626;',
      '  transform: translateY(-1px);',
      '}',
      '@keyframes muqtarin-toast-pulse {',
      '  0% { transform: scale(1); }',
      '  50% { transform: scale(1.025); }',
      '  100% { transform: scale(1); }',
      '}',
      '.muqtarin-toast-pulse { animation: muqtarin-toast-pulse 0.28s ease; }',
      '@media (max-width: 576px) {',
      '  .iziToast-wrapper.iziToast-wrapper-topRight,',
      '  .iziToast-wrapper.iziToast-wrapper-topLeft {',
      '    width: 100% !important;',
      '    max-width: 100% !important;',
      '    padding: 10px !important;',
      '  }',
      '}'
    ].join('\n');
    document.head.appendChild(style);
  }

  function detectType(toastEl) {
    if (toastEl.getAttribute('data-muqtarin-type')) {
      return toastEl.getAttribute('data-muqtarin-type');
    }
    var cls = toastEl.className || '';
    if (cls.indexOf('iziToast-error') !== -1 || cls.indexOf('iziToast-color-red') !== -1) return 'error';
    if (cls.indexOf('iziToast-color-green') !== -1) return 'success';
    if (cls.indexOf('iziToast-color-orange') !== -1 || cls.indexOf('iziToast-color-yellow') !== -1) return 'warning';
    if (cls.indexOf('iziToast-color-blue') !== -1) return 'info';
    return 'info';
  }

  function enhanceToastNode(toastEl) {
    if (!toastEl || toastEl.nodeType !== 1 || !toastEl.classList.contains('iziToast')) return;
    if (toastEl.dataset.muqtarinEnhanced === '1') return;
    toastEl.dataset.muqtarinEnhanced = '1';

    var type = detectType(toastEl);
    toastEl.setAttribute('data-muqtarin-type', type);

    // Ensure .iziToast-body exists and inject SVG icon badge
    var body = toastEl.querySelector('.iziToast-body');
    if (body) {
      var iconEl = body.querySelector('.iziToast-icon');
      if (!iconEl) {
        iconEl = document.createElement('i');
        iconEl.className = 'iziToast-icon';
        body.insertBefore(iconEl, body.firstChild);
      }
      iconEl.className = 'iziToast-icon';
      iconEl.innerHTML = ICONS[type] || ICONS.info;
    }

    // Replace close icon image with crisp SVG
    var closeBtn = toastEl.querySelector('.iziToast-close');
    if (closeBtn) {
      closeBtn.innerHTML = CLOSE_SVG;
    }
  }

  function syncClearAllButton(wrapper) {
    if (!wrapper) return;
    var activeToasts = wrapper.querySelectorAll('.iziToast:not(.iziToast-closing)');
    var existingBtn = wrapper.querySelector('.muqtarin-toast-clear-all');

    if (activeToasts.length >= 3) {
      var isRtl = document.documentElement.getAttribute('dir') === 'rtl';
      var labelText = isRtl
        ? 'إغلاق الكل (' + activeToasts.length + ')'
        : 'Dismiss all (' + activeToasts.length + ')';

      if (!existingBtn) {
        existingBtn = document.createElement('button');
        existingBtn.type = 'button';
        existingBtn.className = 'muqtarin-toast-clear-all';
        existingBtn.addEventListener('click', function () {
          if (window.iziToast && typeof window.iziToast.destroy === 'function') {
            window.iziToast.destroy();
          } else {
            wrapper.innerHTML = '';
          }
          var btn = wrapper.querySelector('.muqtarin-toast-clear-all');
          if (btn) btn.remove();
        });
        wrapper.insertBefore(existingBtn, wrapper.firstChild);
      }
      existingBtn.innerHTML = CLOSE_SVG + '<span>' + labelText + '</span>';
    } else if (existingBtn) {
      existingBtn.remove();
    }
  }

  function attachWrapperObserver(wrapper) {
    if (!wrapper || wrapper.dataset.muqtarinObserved === '1') return;
    wrapper.dataset.muqtarinObserved = '1';

    wrapper.querySelectorAll('.iziToast').forEach(enhanceToastNode);
    syncClearAllButton(wrapper);

    var obs = new MutationObserver(function () {
      wrapper.querySelectorAll('.iziToast').forEach(enhanceToastNode);
      syncClearAllButton(wrapper);
    });
    obs.observe(wrapper, { childList: true, subtree: true });
  }

  function sanitizeOptions(opts, defaultType) {
    opts = opts || {};
    var nextOpts = Object.assign({}, opts, {
      layout: 2,
      animateInside: false,
      transitionIn: (!opts.transitionIn || opts.transitionIn === 'bounceInUp') ? 'fadeInDown' : opts.transitionIn,
      transitionOut: (!opts.transitionOut || opts.transitionOut === 'fadeOut') ? 'fadeOutUp' : opts.transitionOut,
      transitionInMobile: 'fadeInDown',
      transitionOutMobile: 'fadeOutUp'
    });
    if (defaultType === 'error') {
      nextOpts.class = ((nextOpts.class || '') + ' iziToast-error').trim();
    }
    return nextOpts;
  }

  function hookIziToast() {
    if (!window.iziToast || window.iziToast.__muqtarinHooked) return;
    window.iziToast.__muqtarinHooked = true;

    var origSettings = window.iziToast.settings;
    if (typeof origSettings === 'function') {
      window.iziToast.settings = function (opts) {
        opts = Object.assign({}, opts || {}, {
          transitionIn: 'fadeInDown',
          transitionOut: 'fadeOutUp',
          transitionInMobile: 'fadeInDown',
          transitionOutMobile: 'fadeOutUp',
          animateInside: false,
          layout: 2
        });
        return origSettings.call(window.iziToast, opts);
      };
    }

    window.iziToast.settings({
      timeout: 5500,
      resetOnHover: true,
      close: true,
      progressBar: true,
      pauseOnHover: true
    });

    function isDuplicateVisible(type, message) {
      if (!message) return false;
      var msgClean = String(message).replace(/<[^>]*>/g, '').trim();
      var toasts = document.querySelectorAll('.iziToast-wrapper .iziToast:not(.iziToast-closing)');
      for (var i = 0; i < toasts.length; i++) {
        var t = toasts[i];
        var tType = detectType(t);
        var msgEl = t.querySelector('.iziToast-message');
        var existingText = msgEl ? msgEl.textContent.trim() : '';
        if (tType === type && existingText === msgClean) {
          t.classList.remove('muqtarin-toast-pulse');
          void t.offsetWidth;
          t.classList.add('muqtarin-toast-pulse');
          return true;
        }
      }
      return false;
    }

    ['success', 'error', 'warning', 'info'].forEach(function (method) {
      var orig = window.iziToast[method];
      if (typeof orig !== 'function') return;

      window.iziToast[method] = function (opts) {
        opts = opts || {};
        if (isDuplicateVisible(method, opts.message)) {
          return;
        }
        var res = orig.call(window.iziToast, sanitizeOptions(opts, method));
        document.querySelectorAll('.iziToast-wrapper').forEach(attachWrapperObserver);
        return res;
      };
    });

    if (typeof window.iziToast.show === 'function') {
      var origShow = window.iziToast.show;
      window.iziToast.show = function (opts) {
        opts = opts || {};
        var typeHint = 'info';
        if (opts.color === 'red' || (opts.class && opts.class.indexOf('iziToast-error') !== -1)) typeHint = 'error';
        else if (opts.color === 'green') typeHint = 'success';
        else if (opts.color === 'orange' || opts.color === 'yellow') typeHint = 'warning';

        if (isDuplicateVisible(typeHint, opts.message)) {
          return;
        }
        var res = origShow.call(window.iziToast, sanitizeOptions(opts, typeHint));
        document.querySelectorAll('.iziToast-wrapper').forEach(attachWrapperObserver);
        return res;
      };
    }
  }

  var bodyObserved = false;

  function init() {
    if (document.head) injectStyles();
    hookIziToast();
    document.querySelectorAll('.iziToast-wrapper').forEach(attachWrapperObserver);

    if (document.body && !bodyObserved) {
      bodyObserved = true;
      var bodyObs = new MutationObserver(function (mutations) {
        for (var i = 0; i < mutations.length; i++) {
          var nodes = mutations[i].addedNodes;
          for (var j = 0; j < nodes.length; j++) {
            var n = nodes[j];
            if (n.nodeType === 1 && n.classList && n.classList.contains('iziToast-wrapper')) {
              attachWrapperObserver(n);
            }
          }
        }
      });
      bodyObs.observe(document.body, { childList: true });
    }
  }

  if (document.head) injectStyles();
  hookIziToast();

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
  window.addEventListener('load', init);
})();

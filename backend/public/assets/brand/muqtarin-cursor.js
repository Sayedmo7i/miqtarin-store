/* =============================================================================
   MUQTARIN Store — Precision SmoothCursor
   Zero-lag 1:1 pointer hotspot + decoupled spring-physics halo ring.
   Automatically restores native OS cursors on text inputs, selects, iframes,
   disabled controls, drag handles, and touch/reduced-motion environments.
   Brand colors: Pine #0F4C3A | Deep Pine #082B21 | Emerald #22C55E | Gold #F6C667
   ============================================================================= */
(function () {
  'use strict';

  if (window.__muqtarinCursorInit) return;
  window.__muqtarinCursorInit = true;

  var reducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var hasFinePointer = window.matchMedia && window.matchMedia('(pointer: fine)').matches;

  if (reducedMotion || !hasFinePointer) {
    return;
  }

  // Inject minimal scoped styles once
  if (!document.getElementById('muqtarin-smooth-cursor-style')) {
    var style = document.createElement('style');
    style.id = 'muqtarin-smooth-cursor-style';
    style.textContent = [
      'html.muqtarin-cursor-active:not(.muqtarin-cursor-native),',
      'html.muqtarin-cursor-active:not(.muqtarin-cursor-native) * {',
      '  cursor: none !important;',
      '}',
      'html.muqtarin-cursor-active input:not([type="checkbox"]):not([type="radio"]):not([type="submit"]):not([type="button"]):not([type="reset"]):not([type="range"]):not([type="color"]):not([type="file"]),',
      'html.muqtarin-cursor-active textarea,',
      'html.muqtarin-cursor-active [contenteditable="true"] {',
      '  cursor: text !important;',
      '}',
      '#muqtarin-smooth-cursor {',
      '  position: fixed;',
      '  inset: 0;',
      '  z-index: 2147483647;',
      '  pointer-events: none;',
      '  opacity: 0;',
      '  transition: opacity 0.14s ease;',
      '}',
      '#muqtarin-smooth-cursor.is-visible { opacity: 1; }',
      '#muqtarin-smooth-cursor .muqtarin-cursor-ring {',
      '  position: fixed;',
      '  top: 0;',
      '  left: 0;',
      '  width: 24px;',
      '  height: 24px;',
      '  margin-left: -12px;',
      '  margin-top: -12px;',
      '  border-radius: 9999px;',
      '  border: 1.5px solid rgba(15, 76, 58, 0.32);',
      '  background: rgba(34, 197, 94, 0.04);',
      '  opacity: 0.28;',
      '  will-change: transform, opacity;',
      '  transition: opacity 0.18s ease, border-color 0.18s ease, background-color 0.18s ease, box-shadow 0.18s ease;',
      '  pointer-events: none;',
      '}',
      '[data-theme="dark"] #muqtarin-smooth-cursor .muqtarin-cursor-ring {',
      '  border-color: rgba(74, 222, 128, 0.36);',
      '  background: rgba(34, 197, 94, 0.05);',
      '}',
      '#muqtarin-smooth-cursor.is-interactive .muqtarin-cursor-ring {',
      '  opacity: 0.92;',
      '  border-color: rgba(34, 197, 94, 0.88);',
      '  background: rgba(34, 197, 94, 0.12);',
      '  box-shadow: 0 0 14px rgba(34, 197, 94, 0.24);',
      '}',
      '#muqtarin-smooth-cursor.is-pressed .muqtarin-cursor-ring {',
      '  opacity: 1;',
      '  border-color: #F6C667;',
      '  background: rgba(246, 198, 103, 0.22);',
      '  box-shadow: 0 0 16px rgba(246, 198, 103, 0.36);',
      '}',
      '#muqtarin-smooth-cursor .muqtarin-cursor-pointer {',
      '  position: fixed;',
      '  top: 0;',
      '  left: 0;',
      '  will-change: transform;',
      '  pointer-events: none;',
      '}',
      '#muqtarin-smooth-cursor .muqtarin-cursor-arrow {',
      '  display: block;',
      '  width: 24px;',
      '  height: 24px;',
      '  margin-left: -3px;',
      '  margin-top: -2.5px;',
      '  transform-origin: 3px 2.5px;',
      '  filter: drop-shadow(0 2px 5px rgba(8, 43, 33, 0.35)) drop-shadow(0 0 1px rgba(15, 23, 42, 0.45));',
      '  transition: filter 0.15s ease;',
      '}',
      '#muqtarin-smooth-cursor .muqtarin-cursor-facet {',
      '  fill: #14634C;',
      '  transition: fill 0.14s ease;',
      '}',
      '#muqtarin-smooth-cursor.is-interactive .muqtarin-cursor-facet {',
      '  fill: #22C55E;',
      '}',
      '#muqtarin-smooth-cursor.is-pressed .muqtarin-cursor-facet {',
      '  fill: #F6C667;',
      '}',
      '#muqtarin-smooth-cursor.is-interactive .muqtarin-cursor-arrow {',
      '  filter: drop-shadow(0 3px 8px rgba(15, 76, 58, 0.42)) drop-shadow(0 0 1px rgba(34, 197, 94, 0.5));',
      '}',
      '[data-theme="dark"] #muqtarin-smooth-cursor .muqtarin-cursor-arrow {',
      '  filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.7)) drop-shadow(0 0 1px rgba(255, 255, 255, 0.35));',
      '}',
      '@media (pointer: coarse) { #muqtarin-smooth-cursor { display: none !important; } }'
    ].join('\n');
    document.head.appendChild(style);
  }

  function createCursorEl() {
    var existing = document.getElementById('muqtarin-smooth-cursor');
    if (existing) return existing;

    var el = document.createElement('div');
    el.id = 'muqtarin-smooth-cursor';
    el.setAttribute('aria-hidden', 'true');
    el.innerHTML = [
      '<div class="muqtarin-cursor-ring"></div>',
      '<div class="muqtarin-cursor-pointer">',
      '  <svg class="muqtarin-cursor-arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none">',
      '    <path d="M3 2.5L19.5 11.1L12.1 13.3L8.8 20.4L3 2.5Z" fill="#0B2E24" stroke="#FFFFFF" stroke-width="1.75" stroke-linejoin="round"/>',
      '    <path class="muqtarin-cursor-facet" d="M6.4 6.2L15.1 10.7L11.1 11.9L9.4 15.6L6.4 6.2Z"/>',
      '  </svg>',
      '</div>'
    ].join('');
    document.body.appendChild(el);
    return el;
  }

  var cursorEl = null;
  var pointerEl = null;
  var arrowEl = null;
  var ringEl = null;

  function ensureMounted() {
    if (!document.body) return false;
    if (!cursorEl || !document.body.contains(cursorEl)) {
      cursorEl = createCursorEl();
      pointerEl = cursorEl.querySelector('.muqtarin-cursor-pointer');
      arrowEl = cursorEl.querySelector('.muqtarin-cursor-arrow');
      ringEl = cursorEl.querySelector('.muqtarin-cursor-ring');
    }
    return true;
  }

  // Exact pointer coordinates (1:1 zero lag) + spring state for ring & micro-motion
  var targetX = -100, targetY = -100;
  var ringX = -100, ringY = -100;
  var ringVelX = 0, ringVelY = 0;

  var targetAngle = 0;
  var currentAngle = 0;
  var angleVel = 0;

  var targetScale = 1;
  var currentScale = 1;
  var scaleVel = 0;

  var targetRingScale = 0.85;
  var currentRingScale = 0.85;
  var ringScaleVel = 0;

  var lastMouseX = 0, lastMouseY = 0, lastMoveTime = 0;
  var isVisible = false;
  var isNativeTarget = false;
  var isPressed = false;
  var isInteractive = false;
  var rafId = null;
  var lastFrameTime = 0;
  var idleTimer = null;

  var INTERACTIVE_SELECTOR = [
    'a',
    'button',
    '[role="button"]',
    '[role="tab"]',
    '[role="menuitem"]',
    '[role="switch"]',
    'input[type="submit"]',
    'input[type="button"]',
    'input[type="reset"]',
    'input[type="checkbox"]',
    'input[type="radio"]',
    'label[for]',
    '.btn',
    '.nav-link',
    '.page-link',
    '.sidebar-section-trigger',
    '.dropdown-item',
    '[data-bs-toggle]',
    '[data-bs-dismiss]',
    'summary',
    '.muqtarin-icon-btn',
    '.mq-theme-card',
    '.mq-style-card',
    '.mq-preset-card',
    '.mq-preview-ctrl',
    '.select2-selection',
    '.form-check-label',
    '[onclick]'
  ].join(', ');

  var NATIVE_SELECTOR = [
    'input:not([type="checkbox"]):not([type="radio"]):not([type="submit"]):not([type="button"]):not([type="reset"])',
    'textarea',
    'select',
    'iframe',
    '[contenteditable="true"]',
    '.tox-tinymce',
    '.ck-editor',
    '.note-editor',
    '.select2-search__field',
    '.noUi-handle',
    '[draggable="true"]',
    ':disabled',
    '.disabled',
    '[aria-disabled="true"]'
  ].join(', ');

  function unwrapAngle(prev, next) {
    var diff = next - prev;
    while (diff > 180) diff -= 360;
    while (diff < -180) diff += 360;
    return prev + diff;
  }

  function updateVisibility() {
    if (!cursorEl) return;
    var shouldShow = isVisible && !isNativeTarget;
    cursorEl.classList.toggle('is-visible', shouldShow);
    cursorEl.classList.toggle('is-interactive', isInteractive && !isNativeTarget);
    cursorEl.classList.toggle('is-pressed', isPressed && !isNativeTarget);
    document.documentElement.classList.toggle('muqtarin-cursor-native', isNativeTarget);
  }

  function stepSpring(current, target, velocity, stiffness, damping, mass, dt) {
    var force = -stiffness * (current - target);
    var damper = -damping * velocity;
    var accel = (force + damper) / mass;
    var nextVel = velocity + accel * dt;
    var nextPos = current + nextVel * dt;
    return [nextPos, nextVel];
  }

  function renderPointerTransform() {
    if (pointerEl) {
      pointerEl.style.transform = 'translate3d(' + targetX.toFixed(1) + 'px, ' + targetY.toFixed(1) + 'px, 0)';
    }
    if (arrowEl) {
      arrowEl.style.transform = 'rotate(' + currentAngle.toFixed(2) + 'deg) scale(' + currentScale.toFixed(3) + ')';
    }
  }

  function animate(now) {
    if (!ensureMounted()) {
      rafId = null;
      return;
    }

    if (!lastFrameTime) lastFrameTime = now;
    var dt = Math.min((now - lastFrameTime) / 1000, 0.032);
    lastFrameTime = now;

    if (dt > 0) {
      var rx = stepSpring(ringX, targetX, ringVelX, 380, 28, 0.65, dt);
      ringX = rx[0]; ringVelX = rx[1];

      var ry = stepSpring(ringY, targetY, ringVelY, 380, 28, 0.65, dt);
      ringY = ry[0]; ringVelY = ry[1];

      var sa = stepSpring(currentAngle, targetAngle, angleVel, 320, 26, 0.75, dt);
      currentAngle = sa[0]; angleVel = sa[1];

      var ss = stepSpring(currentScale, targetScale, scaleVel, 540, 28, 0.65, dt);
      currentScale = ss[0]; scaleVel = ss[1];

      var rs = stepSpring(currentRingScale, targetRingScale, ringScaleVel, 420, 26, 0.7, dt);
      currentRingScale = rs[0]; ringScaleVel = rs[1];
    }

    renderPointerTransform();
    if (ringEl) {
      ringEl.style.transform = 'translate3d(' + ringX.toFixed(2) + 'px, ' + ringY.toFixed(2) + 'px, 0) scale(' + currentRingScale.toFixed(3) + ')';
    }

    var moving =
      Math.abs(targetX - ringX) > 0.1 ||
      Math.abs(targetY - ringY) > 0.1 ||
      Math.abs(ringVelX) > 0.1 ||
      Math.abs(ringVelY) > 0.1 ||
      Math.abs(targetAngle - currentAngle) > 0.1 ||
      Math.abs(targetScale - currentScale) > 0.002 ||
      Math.abs(targetRingScale - currentRingScale) > 0.002;

    if (moving && isVisible) {
      rafId = requestAnimationFrame(animate);
    } else {
      rafId = null;
      lastFrameTime = 0;
    }
  }

  function startLoop() {
    if (!rafId) {
      lastFrameTime = 0;
      rafId = requestAnimationFrame(animate);
    }
  }

  function syncTargets() {
    if (isPressed) {
      targetScale = 0.86;
      targetRingScale = 0.72;
    } else if (isInteractive) {
      targetScale = 1.06;
      targetRingScale = 1.38;
      targetAngle = unwrapAngle(currentAngle, 0);
    } else {
      targetScale = 1;
      targetRingScale = 0.88;
    }
  }

  function onPointerMove(e) {
    if (e.pointerType && e.pointerType !== 'mouse') {
      disableCursor();
      return;
    }
    if (!ensureMounted()) return;

    document.documentElement.classList.add('muqtarin-cursor-active');

    var now = performance.now();
    if (!isVisible) {
      ringX = e.clientX;
      ringY = e.clientY;
      lastMouseX = e.clientX;
      lastMouseY = e.clientY;
      lastMoveTime = now;
      isVisible = true;
    }

    targetX = e.clientX;
    targetY = e.clientY;
    renderPointerTransform();

    var target = e.target && e.target.nodeType === 1 ? e.target : null;
    isNativeTarget = !!(target && target.closest(NATIVE_SELECTOR));
    isInteractive = !isNativeTarget && !!(target && target.closest(INTERACTIVE_SELECTOR));

    var dt = Math.max(now - lastMoveTime, 8);
    var dx = e.clientX - lastMouseX;
    var dy = e.clientY - lastMouseY;
    var dist = Math.hypot(dx, dy);

    if (dist > 2 && !isInteractive && !isNativeTarget) {
      var velocityTilt = ((dx * 0.6 - dy * 0.15) / dt) * 3.5;
      var clampedTilt = Math.max(-7, Math.min(7, velocityTilt));
      targetAngle = unwrapAngle(currentAngle, clampedTilt);
    }

    lastMouseX = e.clientX;
    lastMouseY = e.clientY;
    lastMoveTime = now;

    if (idleTimer) clearTimeout(idleTimer);
    idleTimer = setTimeout(function () {
      targetAngle = unwrapAngle(currentAngle, 0);
      startLoop();
    }, 90);

    syncTargets();
    updateVisibility();
    startLoop();
  }

  function onPointerDown(e) {
    if (e.pointerType && e.pointerType !== 'mouse') return;
    isPressed = true;
    syncTargets();
    updateVisibility();
    startLoop();
  }

  function onPointerUp(e) {
    if (e.pointerType && e.pointerType !== 'mouse') return;
    isPressed = false;
    syncTargets();
    updateVisibility();
    startLoop();
  }

  function hideCursor() {
    isVisible = false;
    isPressed = false;
    updateVisibility();
  }

  function disableCursor() {
    hideCursor();
    document.documentElement.classList.remove('muqtarin-cursor-active', 'muqtarin-cursor-native');
  }

  document.addEventListener('pointermove', onPointerMove, { passive: true });
  document.addEventListener('pointerdown', onPointerDown, { passive: true });
  document.addEventListener('pointerup', onPointerUp, { passive: true });
  document.addEventListener('mouseleave', hideCursor);
  document.documentElement.addEventListener('pointerleave', hideCursor);
  window.addEventListener('blur', hideCursor);
  document.addEventListener('visibilitychange', function () {
    if (document.hidden) hideCursor();
  });
  document.addEventListener('touchstart', disableCursor, { passive: true });

  // Support Livewire / dynamic SPA navigation cleanly
  document.addEventListener('livewire:navigated', function () {
    ensureMounted();
  });

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', ensureMounted);
  } else {
    ensureMounted();
  }
})();

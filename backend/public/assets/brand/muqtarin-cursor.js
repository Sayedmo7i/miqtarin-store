/* =============================================================================
   MUQTARIN Store — SmoothCursor (MagicUI-inspired Spring Physics Custom Cursor)
   Disabled automatically on touch devices & prefers-reduced-motion.
   Brand colors: Pine #0F4C3A | Emerald #22C55E | Gold #F6C667
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
      'html.muqtarin-cursor-active, html.muqtarin-cursor-active * { cursor: none !important; }',
      'html.muqtarin-cursor-active input[type="text"],',
      'html.muqtarin-cursor-active input[type="password"],',
      'html.muqtarin-cursor-active input[type="email"],',
      'html.muqtarin-cursor-active input[type="search"],',
      'html.muqtarin-cursor-active input[type="number"],',
      'html.muqtarin-cursor-active input[type="url"],',
      'html.muqtarin-cursor-active input[type="tel"],',
      'html.muqtarin-cursor-active textarea,',
      'html.muqtarin-cursor-active [contenteditable="true"] { cursor: text !important; }',
      '#muqtarin-smooth-cursor {',
      '  position: fixed;',
      '  top: 0;',
      '  left: 0;',
      '  z-index: 2147483647;',
      '  pointer-events: none;',
      '  will-change: transform, opacity;',
      '  opacity: 0;',
      '  transition: opacity 0.16s ease;',
      '  transform: translate3d(-100px, -100px, 0);',
      '}',
      '#muqtarin-smooth-cursor.is-visible { opacity: 1; }',
      '#muqtarin-smooth-cursor .muqtarin-cursor-arrow {',
      '  display: block;',
      '  width: 28px;',
      '  height: 28px;',
      '  margin-left: -5.5px;',
      '  margin-top: -3.5px;',
      '  filter: drop-shadow(0 3px 8px rgba(15, 76, 58, 0.28));',
      '  transform-origin: 5.5px 3.5px;',
      '}',
      '#muqtarin-smooth-cursor .muqtarin-cursor-ring {',
      '  position: fixed;',
      '  top: 0;',
      '  left: 0;',
      '  width: 28px;',
      '  height: 28px;',
      '  margin-left: -14px;',
      '  margin-top: -14px;',
      '  border-radius: 9999px;',
      '  border: 1.5px solid rgba(34, 197, 94, 0.55);',
      '  background: rgba(34, 197, 94, 0.08);',
      '  opacity: 0;',
      '  transform: scale(0.5);',
      '  transition: opacity 0.18s ease, transform 0.2s cubic-bezier(0.22, 1, 0.36, 1), border-color 0.18s ease;',
      '  pointer-events: none;',
      '}',
      '#muqtarin-smooth-cursor.is-interactive .muqtarin-cursor-ring {',
      '  opacity: 1;',
      '  transform: scale(1.15);',
      '}',
      '#muqtarin-smooth-cursor.is-pressed .muqtarin-cursor-ring {',
      '  opacity: 1;',
      '  transform: scale(0.85);',
      '  border-color: #F6C667;',
      '  background: rgba(246, 198, 103, 0.16);',
      '}',
      '[data-theme="dark"] #muqtarin-smooth-cursor .muqtarin-cursor-arrow {',
      '  filter: drop-shadow(0 3px 10px rgba(0, 0, 0, 0.55));',
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
      '<svg class="muqtarin-cursor-arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" fill="none">',
      '  <path d="M5.5 3.5L25.5 14.5L15.8 17.2L11.8 26.5L5.5 3.5Z" fill="#0F4C3A" stroke="#FFFFFF" stroke-width="2" stroke-linejoin="round"/>',
      '  <circle cx="15.2" cy="15.8" r="1.8" fill="#22C55E"/>',
      '</svg>'
    ].join('');
    document.body.appendChild(el);
    return el;
  }

  var cursorEl = null;
  var arrowEl = null;

  function ensureMounted() {
    if (!document.body) return false;
    if (!cursorEl || !document.body.contains(cursorEl)) {
      cursorEl = createCursorEl();
      arrowEl = cursorEl.querySelector('.muqtarin-cursor-arrow');
    }
    return true;
  }

  // Spring state
  var targetX = -100, targetY = -100;
  var posX = -100, posY = -100;
  var velX = 0, velY = 0;

  var baseAngle = 0;
  var targetAngle = 0;
  var currentAngle = 0;
  var angleVel = 0;

  var targetScale = 1;
  var currentScale = 1;
  var scaleVel = 0;

  var lastMouseX = 0, lastMouseY = 0, lastMoveTime = 0;
  var isVisible = false;
  var isTextTarget = false;
  var isPressed = false;
  var isInteractive = false;
  var rafId = null;
  var lastFrameTime = 0;
  var idleTimer = null;

  var INTERACTIVE_SELECTOR = 'a, button, [role="button"], input[type="submit"], input[type="button"], input[type="checkbox"], input[type="radio"], select, label[for], .btn, .nav-link, .sidebar-section-trigger, .dropdown-item, [data-bs-toggle], summary, .muqtarin-icon-btn';
  var TEXT_SELECTOR = 'input[type="text"], input[type="password"], input[type="email"], input[type="search"], input[type="number"], input[type="url"], input[type="tel"], input:not([type]), textarea, [contenteditable="true"]';

  function unwrapAngle(prev, next) {
    var diff = next - prev;
    while (diff > 180) diff -= 360;
    while (diff < -180) diff += 360;
    return prev + diff;
  }

  function updateVisibility() {
    if (!cursorEl) return;
    var shouldShow = isVisible && !isTextTarget;
    cursorEl.classList.toggle('is-visible', shouldShow);
    cursorEl.classList.toggle('is-interactive', isInteractive && !isTextTarget);
    cursorEl.classList.toggle('is-pressed', isPressed && !isTextTarget);
  }

  function stepSpring(current, target, velocity, stiffness, damping, mass, dt) {
    var force = -stiffness * (current - target);
    var damper = -damping * velocity;
    var accel = (force + damper) / mass;
    var nextVel = velocity + accel * dt;
    var nextPos = current + nextVel * dt;
    return [nextPos, nextVel];
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
      var sx = stepSpring(posX, targetX, velX, 520, 34, 0.65, dt);
      posX = sx[0]; velX = sx[1];

      var sy = stepSpring(posY, targetY, velY, 520, 34, 0.65, dt);
      posY = sy[0]; velY = sy[1];

      var sa = stepSpring(currentAngle, targetAngle, angleVel, 300, 24, 0.8, dt);
      currentAngle = sa[0]; angleVel = sa[1];

      var ss = stepSpring(currentScale, targetScale, scaleVel, 500, 26, 0.7, dt);
      currentScale = ss[0]; scaleVel = ss[1];
    }

    cursorEl.style.transform = 'translate3d(' + posX.toFixed(2) + 'px, ' + posY.toFixed(2) + 'px, 0)';
    if (arrowEl) {
      arrowEl.style.transform = 'rotate(' + currentAngle.toFixed(2) + 'deg) scale(' + currentScale.toFixed(3) + ')';
    }

    var moving =
      Math.abs(targetX - posX) > 0.08 ||
      Math.abs(targetY - posY) > 0.08 ||
      Math.abs(velX) > 0.08 ||
      Math.abs(velY) > 0.08 ||
      Math.abs(targetAngle - currentAngle) > 0.1 ||
      Math.abs(targetScale - currentScale) > 0.002;

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

  function onPointerMove(e) {
    if (e.pointerType && e.pointerType !== 'mouse') {
      disableCursor();
      return;
    }
    if (!ensureMounted()) return;

    document.documentElement.classList.add('muqtarin-cursor-active');

    var now = performance.now();
    if (!isVisible) {
      posX = e.clientX;
      posY = e.clientY;
      lastMouseX = e.clientX;
      lastMouseY = e.clientY;
      lastMoveTime = now;
      isVisible = true;
    }

    targetX = e.clientX;
    targetY = e.clientY;

    var dt = Math.max(now - lastMoveTime, 8);
    var dx = e.clientX - lastMouseX;
    var dy = e.clientY - lastMouseY;
    var dist = Math.hypot(dx, dy);

    if (dist > 2) {
      var velocityTilt = ((dx * 0.75 - dy * 0.25) / dt) * 9.5;
      var clampedTilt = Math.max(-24, Math.min(24, velocityTilt));
      targetAngle = unwrapAngle(currentAngle, clampedTilt);
    }

    lastMouseX = e.clientX;
    lastMouseY = e.clientY;
    lastMoveTime = now;

    if (idleTimer) clearTimeout(idleTimer);
    idleTimer = setTimeout(function () {
      targetAngle = unwrapAngle(currentAngle, baseAngle);
      startLoop();
    }, 120);

    var target = e.target && e.target.nodeType === 1 ? e.target : null;
    isTextTarget = !!(target && (target.closest(TEXT_SELECTOR) || target.tagName === 'IFRAME'));
    isInteractive = !isTextTarget && !!(target && target.closest(INTERACTIVE_SELECTOR));

    if (!isPressed) {
      targetScale = isInteractive ? 1.1 : 1;
    }

    updateVisibility();
    startLoop();
  }

  function onPointerDown(e) {
    if (e.pointerType && e.pointerType !== 'mouse') return;
    isPressed = true;
    targetScale = 0.84;
    updateVisibility();
    startLoop();
  }

  function onPointerUp(e) {
    if (e.pointerType && e.pointerType !== 'mouse') return;
    isPressed = false;
    targetScale = isInteractive ? 1.1 : 1;
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
    document.documentElement.classList.remove('muqtarin-cursor-active');
  }

  document.addEventListener('pointermove', onPointerMove, { passive: true });
  document.addEventListener('pointerdown', onPointerDown, { passive: true });
  document.addEventListener('pointerup', onPointerUp, { passive: true });
  document.addEventListener('mouseleave', hideCursor);
  window.addEventListener('blur', hideCursor);
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

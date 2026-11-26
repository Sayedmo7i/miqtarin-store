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
      '#muqtarin-smooth-cursor.is-autoscroll .muqtarin-cursor-ring {',
      '  opacity: 0.96;',
      '  border-color: rgba(34, 197, 94, 0.92);',
      '  background: rgba(15, 76, 58, 0.14);',
      '  box-shadow: 0 0 18px rgba(34, 197, 94, 0.28);',
      '}',
      '#muqtarin-smooth-cursor.is-autoscroll.is-autoscroll-moving .muqtarin-cursor-ring {',
      '  border-color: #F6C667;',
      '  background: rgba(246, 198, 103, 0.16);',
      '  box-shadow: 0 0 18px rgba(246, 198, 103, 0.34);',
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
      '  transition: filter 0.15s ease, opacity 0.12s ease;',
      '}',
      '#muqtarin-smooth-cursor.is-autoscroll .muqtarin-cursor-arrow {',
      '  opacity: 0;',
      '}',
      '#muqtarin-smooth-cursor .muqtarin-cursor-scroll-dir {',
      '  position: fixed;',
      '  top: 0;',
      '  left: 0;',
      '  width: 24px;',
      '  height: 24px;',
      '  margin-left: -12px;',
      '  margin-top: -12px;',
      '  transform-origin: 12px 12px;',
      '  opacity: 0;',
      '  pointer-events: none;',
      '  filter: drop-shadow(0 2px 6px rgba(8, 43, 33, 0.4));',
      '  transition: opacity 0.12s ease;',
      '}',
      '#muqtarin-smooth-cursor.is-autoscroll.is-autoscroll-moving .muqtarin-cursor-scroll-dir {',
      '  opacity: 1;',
      '}',
      '#muqtarin-smooth-cursor .muqtarin-scroll-dir-fill {',
      '  fill: #22C55E;',
      '  transition: fill 0.12s ease;',
      '}',
      '#muqtarin-smooth-cursor.is-autoscroll-moving .muqtarin-scroll-dir-fill {',
      '  fill: #F6C667;',
      '}',
      '#muqtarin-autoscroll-anchor {',
      '  position: fixed;',
      '  top: 0;',
      '  left: 0;',
      '  width: 34px;',
      '  height: 34px;',
      '  margin-left: -17px;',
      '  margin-top: -17px;',
      '  border-radius: 9999px;',
      '  z-index: 2147483646;',
      '  pointer-events: none;',
      '  display: flex;',
      '  align-items: center;',
      '  justify-content: center;',
      '  background: rgba(255, 255, 255, 0.9);',
      '  border: 1.5px solid rgba(15, 76, 58, 0.55);',
      '  box-shadow: 0 6px 20px rgba(8, 43, 33, 0.22), 0 0 0 1px rgba(255, 255, 255, 0.55) inset;',
      '  backdrop-filter: blur(6px);',
      '  -webkit-backdrop-filter: blur(6px);',
      '  opacity: 0;',
      '  transform: translate3d(-100px, -100px, 0) scale(0.6);',
      '  transition: opacity 0.14s ease, transform 0.18s cubic-bezier(0.22, 1, 0.36, 1);',
      '}',
      '#muqtarin-autoscroll-anchor.is-active {',
      '  opacity: 1;',
      '}',
      '[data-theme="dark"] #muqtarin-autoscroll-anchor {',
      '  background: rgba(8, 43, 33, 0.9);',
      '  border-color: rgba(74, 222, 128, 0.6);',
      '  box-shadow: 0 6px 22px rgba(0, 0, 0, 0.55), 0 0 0 1px rgba(255, 255, 255, 0.08) inset;',
      '}',
      '#muqtarin-autoscroll-anchor svg {',
      '  width: 24px;',
      '  height: 24px;',
      '  display: block;',
      '}',
      '#muqtarin-autoscroll-anchor .mq-as-arrow {',
      '  opacity: 0.22;',
      '  transition: opacity 0.12s ease, fill 0.12s ease;',
      '}',
      '#muqtarin-autoscroll-anchor.can-y .mq-as-y,',
      '#muqtarin-autoscroll-anchor.can-x .mq-as-x {',
      '  opacity: 0.92;',
      '}',
      '#muqtarin-autoscroll-anchor .mq-as-dot {',
      '  fill: #0F4C3A;',
      '}',
      '[data-theme="dark"] #muqtarin-autoscroll-anchor .mq-as-dot {',
      '  fill: #22C55E;',
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
      '@media (pointer: coarse) { #muqtarin-smooth-cursor, #muqtarin-autoscroll-anchor { display: none !important; } }'
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
      '  <svg class="muqtarin-cursor-scroll-dir" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none">',
      '    <path d="M12 2.5L18.5 11H14.5V19.5H9.5V11H5.5L12 2.5Z" fill="#0B2E24" stroke="#FFFFFF" stroke-width="1.65" stroke-linejoin="round"/>',
      '    <path class="muqtarin-scroll-dir-fill" d="M12 5.4L15.4 9.8H13.2V17.4H10.8V9.8H8.6L12 5.4Z"/>',
      '  </svg>',
      '</div>'
    ].join('');
    document.body.appendChild(el);
    return el;
  }

  function createAnchorEl() {
    var existing = document.getElementById('muqtarin-autoscroll-anchor');
    if (existing) return existing;

    var anchor = document.createElement('div');
    anchor.id = 'muqtarin-autoscroll-anchor';
    anchor.setAttribute('aria-hidden', 'true');
    anchor.innerHTML = [
      '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none">',
      '  <path class="mq-as-arrow mq-as-y" d="M12 3.2L15 7H9L12 3.2Z" fill="#14634C"/>',
      '  <path class="mq-as-arrow mq-as-y" d="M12 20.8L15 17H9L12 20.8Z" fill="#14634C"/>',
      '  <path class="mq-as-arrow mq-as-x" d="M3.2 12L7 9V15L3.2 12Z" fill="#14634C"/>',
      '  <path class="mq-as-arrow mq-as-x" d="M20.8 12L17 9V15L20.8 12Z" fill="#14634C"/>',
      '  <circle class="mq-as-dot" cx="12" cy="12" r="2.6"/>',
      '</svg>'
    ].join('');
    document.body.appendChild(anchor);
    return anchor;
  }

  var cursorEl = null;
  var pointerEl = null;
  var arrowEl = null;
  var scrollDirEl = null;
  var ringEl = null;
  var anchorEl = null;

  function ensureMounted() {
    if (!document.body) return false;
    if (!cursorEl || !document.body.contains(cursorEl)) {
      cursorEl = createCursorEl();
      pointerEl = cursorEl.querySelector('.muqtarin-cursor-pointer');
      arrowEl = cursorEl.querySelector('.muqtarin-cursor-arrow');
      scrollDirEl = cursorEl.querySelector('.muqtarin-cursor-scroll-dir');
      ringEl = cursorEl.querySelector('.muqtarin-cursor-ring');
    }
    if (!anchorEl || !document.body.contains(anchorEl)) {
      anchorEl = createAnchorEl();
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

  // Auto-Scroll (Middle Mouse Button) state
  var AUTO_SCROLL_DEADZONE = 10;
  var autoScrollActive = false;
  var autoScrollMoving = false;
  var autoScrollTarget = null;
  var autoScrollCanX = false;
  var autoScrollCanY = false;
  var autoScrollOriginX = 0;
  var autoScrollOriginY = 0;
  var autoScrollDownTime = 0;
  var autoScrollDragged = false;
  var autoScrollAccumX = 0;
  var autoScrollAccumY = 0;
  var suppressNextAuxClick = false;
  var suppressNextClick = false;

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

  function findScrollableTarget(startEl) {
    var el = startEl && startEl.nodeType === 1 ? startEl : document.documentElement;
    while (el && el !== document.body && el !== document.documentElement) {
      var cs = window.getComputedStyle(el);
      var oy = cs.overflowY;
      var ox = cs.overflowX;
      var canY = (oy === 'auto' || oy === 'scroll' || oy === 'overlay') && el.scrollHeight > el.clientHeight + 2;
      var canX = (ox === 'auto' || ox === 'scroll' || ox === 'overlay') && el.scrollWidth > el.clientWidth + 2;
      if (canY || canX) {
        return { el: el, canY: canY, canX: canX };
      }
      el = el.parentElement;
    }

    var root = document.scrollingElement || document.documentElement;
    var rootCanY = root.scrollHeight > Math.max(window.innerHeight, document.documentElement.clientHeight) + 2;
    var rootCanX = root.scrollWidth > Math.max(window.innerWidth, document.documentElement.clientWidth) + 2;
    if (rootCanY || rootCanX) {
      return { el: root, canY: rootCanY, canX: rootCanX };
    }
    return null;
  }

  function scrollElementBy(el, dx, dy) {
    if (!el) return;
    if (typeof el.scrollBy === 'function') {
      try {
        el.scrollBy({ left: dx, top: dy, behavior: 'instant' });
        return;
      } catch (err) {
        // Fallback for older browsers not supporting behavior: 'instant'
      }
    }
    el.scrollLeft += dx;
    el.scrollTop += dy;
  }

  function updateVisibility() {
    if (!cursorEl) return;
    var effectiveNative = isNativeTarget && !autoScrollActive;
    var shouldShow = isVisible && !effectiveNative;
    cursorEl.classList.toggle('is-visible', shouldShow);
    cursorEl.classList.toggle('is-interactive', isInteractive && !effectiveNative && !autoScrollActive);
    cursorEl.classList.toggle('is-pressed', isPressed && !effectiveNative && !autoScrollActive);
    cursorEl.classList.toggle('is-autoscroll', autoScrollActive);
    cursorEl.classList.toggle('is-autoscroll-moving', autoScrollActive && autoScrollMoving);
    document.documentElement.classList.toggle('muqtarin-cursor-native', effectiveNative);
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

  function stopAutoScroll() {
    if (!autoScrollActive) return;
    autoScrollActive = false;
    autoScrollMoving = false;
    autoScrollTarget = null;
    autoScrollCanX = false;
    autoScrollCanY = false;
    autoScrollAccumX = 0;
    autoScrollAccumY = 0;
    isPressed = false;

    if (anchorEl) {
      anchorEl.classList.remove('is-active', 'can-x', 'can-y');
      anchorEl.style.transform = 'translate3d(' + autoScrollOriginX.toFixed(1) + 'px, ' + autoScrollOriginY.toFixed(1) + 'px, 0) scale(0.6)';
    }
    syncTargets();
    updateVisibility();
    startLoop();
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

    if (autoScrollActive && autoScrollTarget) {
      var rawDx = autoScrollCanX ? (targetX - autoScrollOriginX) : 0;
      var rawDy = autoScrollCanY ? (targetY - autoScrollOriginY) : 0;
      var dist = Math.hypot(rawDx, rawDy);

      if (dist > AUTO_SCROLL_DEADZONE && dt > 0) {
        autoScrollMoving = true;
        var effective = dist - AUTO_SCROLL_DEADZONE;
        var speed = Math.min(2800, 45 + Math.pow(effective, 1.42) * 3.4);
        var ux = rawDx / dist;
        var uy = rawDy / dist;

        autoScrollAccumX += ux * speed * dt;
        autoScrollAccumY += uy * speed * dt;

        var stepX = autoScrollAccumX >= 0 ? Math.floor(autoScrollAccumX) : Math.ceil(autoScrollAccumX);
        var stepY = autoScrollAccumY >= 0 ? Math.floor(autoScrollAccumY) : Math.ceil(autoScrollAccumY);

        if (stepX !== 0 || stepY !== 0) {
          autoScrollAccumX -= stepX;
          autoScrollAccumY -= stepY;
          scrollElementBy(autoScrollTarget, stepX, stepY);
        }

        if (scrollDirEl) {
          var dirDeg = Math.atan2(ux, -uy) * (180 / Math.PI);
          var dirScale = 1 + Math.min(0.2, effective / 850);
          scrollDirEl.style.transform = 'rotate(' + dirDeg.toFixed(1) + 'deg) scale(' + dirScale.toFixed(3) + ')';
        }
      } else {
        autoScrollMoving = false;
        autoScrollAccumX = 0;
        autoScrollAccumY = 0;
      }

      if (cursorEl) {
        cursorEl.classList.toggle('is-autoscroll-moving', autoScrollMoving);
      }
    }

    renderPointerTransform();
    if (ringEl) {
      ringEl.style.transform = 'translate3d(' + ringX.toFixed(2) + 'px, ' + ringY.toFixed(2) + 'px, 0) scale(' + currentRingScale.toFixed(3) + ')';
    }

    var moving =
      autoScrollActive ||
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
    if (autoScrollActive) {
      targetScale = 1;
      targetRingScale = 1.18;
      targetAngle = unwrapAngle(currentAngle, 0);
    } else if (isPressed) {
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

    if (autoScrollActive) {
      if (Math.hypot(targetX - autoScrollOriginX, targetY - autoScrollOriginY) >= 8) {
        autoScrollDragged = true;
      }
      isNativeTarget = false;
      isInteractive = false;
      syncTargets();
      updateVisibility();
      startLoop();
      return;
    }

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

  function onMouseDown(e) {
    // Handle Middle Mouse Button (Auto-Scroll) and exit click-locked Auto-Scroll
    if (autoScrollActive) {
      e.preventDefault();
      if (e.button === 1) {
        suppressNextAuxClick = true;
      } else if (e.button === 0) {
        suppressNextClick = true;
      }
      stopAutoScroll();
      return;
    }

    if (e.button !== 1) return;
    if (!ensureMounted()) return;

    var target = e.target && e.target.nodeType === 1 ? e.target : null;
    // Preserve native middle-click "open in new tab" on links
    if (target && target.closest('a[href], area[href]')) return;
    // Preserve native controls inside inputs, editors, iframes, etc.
    if (target && target.closest(NATIVE_SELECTOR)) return;

    var scrollable = findScrollableTarget(target);
    if (!scrollable) return;

    e.preventDefault();
    suppressNextAuxClick = true;

    document.documentElement.classList.add('muqtarin-cursor-active');
    if (!isVisible) {
      ringX = e.clientX;
      ringY = e.clientY;
      isVisible = true;
    }
    targetX = e.clientX;
    targetY = e.clientY;

    autoScrollActive = true;
    autoScrollMoving = false;
    autoScrollTarget = scrollable.el;
    autoScrollCanX = scrollable.canX;
    autoScrollCanY = scrollable.canY;
    autoScrollOriginX = e.clientX;
    autoScrollOriginY = e.clientY;
    autoScrollDownTime = performance.now();
    autoScrollDragged = false;
    autoScrollAccumX = 0;
    autoScrollAccumY = 0;
    isNativeTarget = false;
    isInteractive = false;

    if (anchorEl) {
      anchorEl.classList.toggle('can-y', autoScrollCanY);
      anchorEl.classList.toggle('can-x', autoScrollCanX);
      anchorEl.style.transform = 'translate3d(' + autoScrollOriginX.toFixed(1) + 'px, ' + autoScrollOriginY.toFixed(1) + 'px, 0) scale(1)';
      anchorEl.classList.add('is-active');
    }

    renderPointerTransform();
    syncTargets();
    updateVisibility();
    startLoop();
  }

  function onMouseUp(e) {
    if (suppressNextClick || suppressNextAuxClick) {
      setTimeout(function () {
        suppressNextClick = false;
        suppressNextAuxClick = false;
      }, 0);
    }
    if (!autoScrollActive || e.button !== 1) return;
    var elapsed = performance.now() - autoScrollDownTime;
    if (autoScrollDragged || elapsed > 240) {
      stopAutoScroll();
    }
  }

  function onAuxClick(e) {
    if (suppressNextAuxClick && e.button === 1) {
      suppressNextAuxClick = false;
      e.preventDefault();
    }
  }

  function onClickCapture(e) {
    if (suppressNextClick) {
      suppressNextClick = false;
      e.preventDefault();
      e.stopPropagation();
    }
  }

  function onKeyDown(e) {
    if (autoScrollActive && e.key === 'Escape') {
      stopAutoScroll();
    }
  }

  function onWheel() {
    if (autoScrollActive) {
      stopAutoScroll();
    }
  }

  function onPointerDown(e) {
    if (e.pointerType && e.pointerType !== 'mouse') return;
    if (autoScrollActive || e.button === 1) return;
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
    stopAutoScroll();
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
  document.addEventListener('mousedown', onMouseDown, { capture: true, passive: false });
  document.addEventListener('mouseup', onMouseUp, { capture: true, passive: true });
  document.addEventListener('auxclick', onAuxClick, { capture: true, passive: false });
  document.addEventListener('click', onClickCapture, { capture: true, passive: false });
  document.addEventListener('keydown', onKeyDown, { passive: true });
  document.addEventListener('wheel', onWheel, { passive: true });
  document.addEventListener('mouseleave', hideCursor);
  document.documentElement.addEventListener('pointerleave', hideCursor);
  window.addEventListener('blur', hideCursor);
  document.addEventListener('visibilitychange', function () {
    if (document.hidden) hideCursor();
  });
  document.addEventListener('touchstart', disableCursor, { passive: true });

  // Support Livewire / dynamic SPA navigation cleanly
  document.addEventListener('livewire:navigated', function () {
    stopAutoScroll();
    ensureMounted();
  });

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', ensureMounted);
  } else {
    ensureMounted();
  }
})();

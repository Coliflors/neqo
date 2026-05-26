/* Protección anti-inspección
 * - Bloquea clic derecho
 * - Bloquea F12, Ctrl+U, Ctrl+S, Ctrl+Shift+I/J/C
 * - Detecta DevTools abierto y redirige a about:blank
 * - Detecta debugger trap para entornos especiales
 *
 * Nota: ningún método garantiza al 100% impedir el acceso al código
 * en el navegador. Esto disuade a usuarios casuales.
 */
(function () {
  'use strict';

  // 1) Clic derecho
  document.addEventListener('contextmenu', function (e) {
    e.preventDefault();
    return false;
  });

  // 2) Selección y arrastre de elementos
  document.addEventListener('selectstart', function (e) {
    var t = e.target;
    if (t && (t.tagName === 'INPUT' || t.tagName === 'TEXTAREA' || t.tagName === 'SELECT')) return;
    e.preventDefault();
  });
  document.addEventListener('dragstart', function (e) { e.preventDefault(); });
  document.addEventListener('copy', function (e) { e.preventDefault(); });

  // 3) Atajos de teclado
  document.addEventListener('keydown', function (e) {
    var k = e.key ? e.key.toLowerCase() : '';
    var code = e.keyCode || e.which;
    // F12
    if (code === 123) { e.preventDefault(); return false; }
    // Ctrl+U (ver fuente)
    if (e.ctrlKey && k === 'u') { e.preventDefault(); return false; }
    // Ctrl+S (guardar)
    if (e.ctrlKey && k === 's') { e.preventDefault(); return false; }
    // Ctrl+Shift+I / J / C (devtools / consola / inspector)
    if (e.ctrlKey && e.shiftKey && (k === 'i' || k === 'j' || k === 'c')) {
      e.preventDefault();
      return false;
    }
    // Cmd+Opt+I / J (Mac)
    if (e.metaKey && e.altKey && (k === 'i' || k === 'j')) {
      e.preventDefault();
      return false;
    }
  });

  // Detectar si es móvil/tablet (touch) — en estos NO aplicamos las
  // detecciones por tamaño ni la trampa de debugger porque:
  //  - El teclado virtual cambia innerHeight y dispara falsos positivos.
  //  - Los navegadores móviles no tienen DevTools accesibles directamente.
  var isTouchDevice =
    ('ontouchstart' in window) ||
    (navigator.maxTouchPoints && navigator.maxTouchPoints > 0) ||
    /Android|iPhone|iPad|iPod|Mobile|Tablet/i.test(navigator.userAgent || '');

  if (!isTouchDevice) {
    // 4) Detección de DevTools por diferencia entre outer/inner size (solo PC)
    var threshold = 200;
    function devtoolsOpen() {
      var w = window.outerWidth - window.innerWidth;
      var h = window.outerHeight - window.innerHeight;
      return w > threshold || h > threshold;
    }

    var blocked = false;
    function block() {
      if (blocked) return;
      blocked = true;
      try { document.body.innerHTML = ''; } catch (_) {}
      try { window.location.replace('about:blank'); } catch (_) {}
    }

    setInterval(function () {
      if (devtoolsOpen()) block();
    }, 800);

    // 5) Trampa de debugger (solo PC)
    setInterval(function () {
      var t = performance.now();
      // eslint-disable-next-line no-debugger
      debugger;
      if (performance.now() - t > 100) block();
    }, 1500);
  }

  // 6) Limpiar consola periódicamente
  if (typeof console !== 'undefined' && console.clear) {
    setInterval(function () { try { console.clear(); } catch (_) {} }, 1200);
  }
})();

